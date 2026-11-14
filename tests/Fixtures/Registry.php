<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Tests\Fixtures;

use PolygonLab\ServeursMcpFrance\Outils\HttpClient;
use PolygonLab\ServeursMcpFrance\Outils\SourceError;

/**
 * Faux registre MCP : pages servies dans l'ordre (tableaux PHP encodés en JSON, ou texte brut), adresses demandées
 * gardées, aucun appel réseau.
 */
final class Registry implements HttpClient
{
    /** @var list<string> */
    public array $requests = [];

    /**
     * @param list<array<mixed>|string> $pages
     */
    public function __construct(private readonly array $pages) {}

    /**
     * Pages d'essai du dépôt (tests/Fixtures/registre), décodées.
     *
     * @return list<array<mixed>>
     */
    public static function fixturePages(): array
    {
        $pages = [];
        foreach (glob(Workspace::PAGES . '/page-*.json') ?: [] as $file) {
            $page = json_decode((string) file_get_contents($file), true);
            if (\is_array($page)) {
                $pages[] = $page;
            }
        }

        return $pages;
    }

    /**
     * Entrée du registre, au format de l'API.
     *
     * @param array<string, mixed> $server champs de « server » en plus du nom et de la version
     *
     * @return array<string, mixed>
     */
    public static function entry(string $name, string $version = '1.0.0', array $server = [], string $status = 'active', ?bool $latest = true, string $updated = '2026-11-01T08:00:00Z'): array
    {
        $official = ['status' => $status, 'publishedAt' => '2026-10-01T08:00:00Z', 'updatedAt' => $updated];
        if ($latest !== null) {
            $official['isLatest'] = $latest;
        }

        return [
            'server' => ['name' => $name, 'version' => $version] + $server,
            '_meta' => ['io.modelcontextprotocol.registry/official' => $official],
        ];
    }

    /**
     * Page de l'API.
     *
     * @param list<array<mixed>> $entries
     *
     * @return array<string, mixed>
     */
    public static function page(array $entries, ?string $next = null): array
    {
        $metadata = ['count' => \count($entries)];
        if ($next !== null) {
            $metadata['nextCursor'] = $next;
        }

        return ['servers' => $entries, 'metadata' => $metadata];
    }

    /**
     * Pages reliées par leur curseur : chaque page, sauf la dernière, annonce la suivante.
     *
     * @param list<array<mixed>> $pages
     *
     * @return list<array<mixed>>
     */
    public static function chain(array $pages): array
    {
        $last = \count($pages) - 1;
        foreach ($pages as $index => $page) {
            $metadata = \is_array($page['metadata'] ?? null) ? $page['metadata'] : [];
            unset($metadata['nextCursor']);
            if ($index < $last) {
                $metadata['nextCursor'] = 'curseur-' . ($index + 1);
            }
            $page['metadata'] = $metadata;
            $pages[$index] = $page;
        }

        return $pages;
    }

    public function get(string $url): string
    {
        $this->requests[] = $url;
        $page = $this->pages[\count($this->requests) - 1] ?? null;
        if ($page === null) {
            throw new SourceError('Registre injoignable après 3 essai(s) : ' . $url . ' (HTTP 503).');
        }

        return \is_string($page) ? $page : (string) json_encode($page);
    }
}
