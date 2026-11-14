<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Collecte : parcourt toute la liste du registre MCP officiel (dernière version de chaque serveur, page par page),
 * puis retient localement les serveurs actifs qui correspondent aux mots-clés.
 *
 *   - pagination par metadata.nextCursor (absent à la dernière page), pause entre deux pages ;
 *   - plafond de pages et durée maximale : arrêt, rien n'est écrit ;
 *   - une entrée sans server.name, server.version ou statut officiel arrête la lecture, entrée citée ;
 *   - tout autre champ est facultatif : absent ou de forme inattendue, il est ignoré ;
 *   - un nom présent plusieurs fois : on garde l'entrée marquée isLatest, sinon la plus récemment mise à jour ;
 *   - seuls les serveurs au statut « active » sont retenus.
 *
 * @phpstan-type Entry array{server: array<mixed>, name: string, version: string, status: string, latest: bool|null, updated: string|null, published: string|null}
 */
final class Collector
{
    /** Format du fichier normalisé écrit par la collecte. */
    public const FORMAT = 1;

    public const ACTIVE = 'active';

    private const META = 'io.modelcontextprotocol.registry/official';

    private const TIMEZONE = 'Europe/Paris';

    private readonly \Closure $sleep;

    private readonly \Closure $clock;

    public function __construct(
        private readonly HttpClient $client,
        private readonly Sources $sources,
        private readonly Keywords $keywords,
        ?\Closure $sleep = null,
        ?\Closure $clock = null,
    ) {
        $this->sleep = $sleep ?? static function (int $seconds): void {
            sleep($seconds);
        };
        $this->clock = $clock ?? static fn(): float => microtime(true);
    }

    /**
     * @return array{servers: list<Server>, pages: int, read: int, active: int}
     *
     * @throws SourceError
     */
    public function collect(): array
    {
        $start = $this->now();
        $cursor = null;
        $seen = [];
        $pages = 0;
        $read = 0;
        /** @var array<string, Entry> $byName */
        $byName = [];
        do {
            if ($pages >= $this->sources->maxPages) {
                throw new SourceError(sprintf('Plafond de %d pages atteint alors que le registre annonce encore une page suivante : collecte arrêtée (relever « pages_max » dans tools/sources.json si le registre a vraiment grandi).', $this->sources->maxPages));
            }
            if ($pages > 0) {
                if ($this->now() - $start > $this->sources->maxDurationSeconds) {
                    throw new SourceError(sprintf('Durée maximale de %d secondes dépassée après %d pages : collecte arrêtée.', $this->sources->maxDurationSeconds, $pages));
                }
                if ($this->sources->pauseSeconds > 0) {
                    ($this->sleep)($this->sources->pauseSeconds);
                }
            }
            $pages++;
            $url = $this->sources->pageUrl($cursor);
            [$entries, $next] = self::page($this->client->get($url), $pages);
            foreach ($entries as $index => $raw) {
                $read++;
                $entry = self::entry($raw, $pages, $index + 1);
                $current = $byName[$entry['name']] ?? null;
                if ($current === null || self::better($entry, $current)) {
                    $byName[$entry['name']] = $entry;
                }
            }
            if ($next !== null && isset($seen[$next])) {
                throw new SourceError(sprintf('Page %d : le curseur suivant « %s » a déjà été servi (boucle) : collecte arrêtée.', $pages, $next));
            }
            if ($next !== null) {
                $seen[$next] = true;
            }
            $cursor = $next;
        } while ($cursor !== null);

        $active = 0;
        $servers = [];
        foreach ($byName as $entry) {
            if ($entry['status'] !== self::ACTIVE) {
                continue;
            }
            $active++;
            $server = self::server($entry);
            if ($this->keywords->firstMatch($server->searchText()) !== null) {
                $servers[] = $server;
            }
        }
        usort($servers, static fn(Server $a, Server $b): int => strcmp($a->name, $b->name));

        return ['servers' => $servers, 'pages' => $pages, 'read' => $read, 'active' => $active];
    }

    /**
     * Fichier normalisé de la collecte : serveurs retenus, triés par nom, et empreinte des règles de sélection.
     * Son empreinte ne change que si ces données changent (jamais pour un simple changement d'ordre des pages).
     *
     * @param list<Server> $servers
     */
    public static function normalizedFile(array $servers, string $rulesHash): string
    {
        return JsonFile::encode([
            'format' => self::FORMAT,
            'regles' => $rulesHash,
            'serveurs' => array_map(static fn(Server $server): array => $server->toArray(), $servers),
        ]);
    }

    /**
     * Serveurs relus d'un fichier normalisé de collecte.
     *
     * @return list<Server>
     *
     * @throws SourceError
     */
    public static function readNormalizedFile(string $content): array
    {
        $label = 'Fichier de collecte';
        $data = JsonFile::decode($content, $label);
        if (($data['format'] ?? null) !== self::FORMAT || !\is_array($data['serveurs'] ?? null) || !array_is_list($data['serveurs'])) {
            throw new SourceError($label . ' : format ' . self::FORMAT . ' et liste « serveurs » attendus.');
        }
        $servers = [];
        foreach ($data['serveurs'] as $item) {
            if (!\is_array($item)) {
                throw new SourceError($label . ' : serveur illisible.');
            }
            $servers[] = Server::fromArray($item, $label);
        }

        return $servers;
    }

    /**
     * @return array{0: list<mixed>, 1: string|null} entrées de la page, curseur de la page suivante
     *
     * @throws SourceError
     */
    private static function page(string $body, int $number): array
    {
        try {
            $data = json_decode($body, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $error) {
            throw new SourceError(sprintf('Page %d : réponse illisible (JSON invalide : %s).', $number, $error->getMessage()));
        }
        $servers = \is_array($data) ? ($data['servers'] ?? null) : null;
        if (!\is_array($servers) || !array_is_list($servers)) {
            throw new SourceError(sprintf('Page %d : liste « servers » absente de la réponse.', $number));
        }
        $metadata = \is_array($data['metadata'] ?? null) ? $data['metadata'] : [];
        $next = $metadata['nextCursor'] ?? null;

        return [$servers, \is_string($next) && $next !== '' ? $next : null];
    }

    /**
     * @return Entry
     *
     * @throws SourceError
     */
    private static function entry(mixed $raw, int $page, int $index): array
    {
        $server = \is_array($raw) ? ($raw['server'] ?? null) : null;
        $server = \is_array($server) ? $server : null;
        $name = $server === null ? null : self::optional($server, 'name');
        $version = $server === null ? null : self::optional($server, 'version');
        $metas = \is_array($raw) ? ($raw['_meta'] ?? null) : null;
        $meta = \is_array($metas) ? ($metas[self::META] ?? null) : null;
        $meta = \is_array($meta) ? $meta : null;
        $status = $meta === null ? null : self::optional($meta, 'status');
        if ($server === null || $name === null) {
            throw self::missing($raw, $page, $index, 'server.name', null);
        }
        if ($version === null) {
            throw self::missing($raw, $page, $index, 'server.version', $name);
        }
        if ($meta === null || $status === null) {
            throw self::missing($raw, $page, $index, '_meta.' . self::META . '.status', $name);
        }
        $latest = $meta['isLatest'] ?? null;

        return [
            'server' => $server,
            'name' => $name,
            'version' => $version,
            'status' => $status,
            'latest' => \is_bool($latest) ? $latest : null,
            'updated' => self::optional($meta, 'updatedAt'),
            'published' => self::optional($meta, 'publishedAt'),
        ];
    }

    private static function missing(mixed $raw, int $page, int $index, string $field, ?string $name): SourceError
    {
        return new SourceError(sprintf(
            'Page %d, entrée n° %d : champ obligatoire « %s » absent ou vide ; entrée : %s',
            $page,
            $index,
            $field,
            $name ?? mb_strimwidth((string) json_encode($raw, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 0, 300, '…'),
        ));
    }

    /**
     * L'entrée remplace-t-elle celle déjà retenue pour ce nom ?
     *
     * @param Entry $candidate
     * @param Entry $current
     */
    private static function better(array $candidate, array $current): bool
    {
        if (($candidate['latest'] === true) !== ($current['latest'] === true)) {
            return $candidate['latest'] === true;
        }

        return self::timestamp($candidate['updated']) > self::timestamp($current['updated']);
    }

    /**
     * @param Entry $entry
     */
    private static function server(array $entry): Server
    {
        $data = $entry['server'];
        $repository = $data['repository'] ?? null;
        $repository = \is_array($repository) ? $repository : [];
        $remotes = [];
        $list = $data['remotes'] ?? null;
        foreach (\is_array($list) ? $list : [] as $remote) {
            $type = \is_array($remote) ? self::optional($remote, 'type') : null;
            $url = \is_array($remote) ? self::optional($remote, 'url') : null;
            if ($type !== null && $url !== null) {
                $remotes[] = ['type' => $type, 'url' => $url];
            }
        }
        $packages = [];
        $list = $data['packages'] ?? null;
        foreach (\is_array($list) ? $list : [] as $package) {
            if (!\is_array($package)) {
                continue;
            }
            $registry = self::firstString($package, ['registryType', 'registry_type', 'registry_name']);
            $identifier = self::firstString($package, ['identifier', 'name']);
            if ($registry !== null && $identifier !== null) {
                $packages[] = ['registre' => $registry, 'identifiant' => $identifier];
            }
        }

        return new Server(
            $entry['name'],
            self::optional($data, 'title'),
            self::optional($data, 'description'),
            $entry['version'],
            self::optional($repository, 'url'),
            self::optional($data, 'websiteUrl'),
            $remotes,
            $packages,
            self::day($entry['published']),
            self::day($entry['updated']),
        );
    }

    /**
     * @param array<mixed> $data
     */
    private static function optional(array $data, string $field): ?string
    {
        $value = $data[$field] ?? null;

        return \is_string($value) ? Text::clean($value) : null;
    }

    /**
     * @param array<mixed> $data
     * @param list<string> $fields
     */
    private static function firstString(array $data, array $fields): ?string
    {
        foreach ($fields as $field) {
            $value = self::optional($data, $field);
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Jour (AAAA-MM-JJ, heure de Paris) d'un horodatage ISO 8601 du registre, ou null s'il est absent ou illisible.
     */
    public static function day(?string $timestamp): ?string
    {
        if ($timestamp === null || preg_match('/\A\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $timestamp) !== 1) {
            return null;
        }
        try {
            return (new \DateTimeImmutable($timestamp))->setTimezone(new \DateTimeZone(self::TIMEZONE))->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    private static function timestamp(?string $timestamp): int
    {
        $time = $timestamp === null ? false : strtotime($timestamp);

        return $time === false ? 0 : $time;
    }

    private function now(): float
    {
        $now = ($this->clock)();

        return \is_float($now) || \is_int($now) ? (float) $now : 0.0;
    }
}
