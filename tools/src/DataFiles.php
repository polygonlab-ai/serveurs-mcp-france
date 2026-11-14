<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Lecture et écriture des fichiers du dossier data/.
 *
 *   serveurs.json   liste courante, clés dans un ordre fixe, serveurs triés par nom ; aucune adresse e-mail
 *   serveurs.csv    même contenu, séparateur « ; », UTF-8 avec BOM, listes aplaties (« a | b »)
 *   historique.csv  un événement par ligne (entrée, sortie, nouvelle version, changement de catégorie, écarté) ;
 *                   ne fait que s'allonger
 *   source.json     adresse lue, date de collecte, pages et entrées lues, empreinte SHA-256 des données normalisées
 *
 * Le rendu est déterministe : mêmes données, mêmes octets.
 *
 * @phpstan-import-type Remote from Server
 * @phpstan-import-type Package from Server
 * @phpstan-type Event array{date: string, evenement: string, nom: string, categorie: string, detail: string}
 */
final class DataFiles
{
    public const SERVERS_JSON = 'serveurs.json';
    public const SERVERS_CSV = 'serveurs.csv';
    public const HISTORY_CSV = 'historique.csv';
    public const SOURCE_JSON = 'source.json';

    public const BOM = "\u{FEFF}";
    public const SEPARATOR = ';';
    public const LIST_SEPARATOR = ' | ';

    public const CSV_COLUMNS = ['nom', 'titre', 'description', 'version', 'categorie', 'depot', 'site', 'distant', 'paquets', 'publie_le', 'mis_a_jour_le'];

    public const HISTORY_COLUMNS = ['date', 'evenement', 'nom', 'categorie', 'detail'];

    public const EVENT_ENTRY = 'entree';
    public const EVENT_EXIT = 'sortie';
    public const EVENT_VERSION = 'nouvelle_version';
    public const EVENT_CATEGORY = 'changement_categorie';
    public const EVENT_DISCARDED = 'ecarte';

    /**
     * @param array<string, string> $categories nom du serveur => catégorie (pour chaque serveur de la liste)
     * @param list<string> $categoryKeys toutes les catégories, dans l'ordre
     * @param list<Server> $servers
     */
    public static function serversJson(Sources $sources, ?string $sourceDate, array $servers, array $categories, array $categoryKeys): string
    {
        $counts = array_fill_keys($categoryKeys, 0);
        $list = [];
        foreach (self::sorted($servers) as $server) {
            $category = $categories[$server->name] ?? Categories::OTHER;
            $counts[$category] = ($counts[$category] ?? 0) + 1;
            $record = $server->toArray();
            $list[] = [
                'nom' => $record['nom'],
                'titre' => $record['titre'],
                'description' => $record['description'],
                'version' => $record['version'],
                'categorie' => $category,
                'depot' => $record['depot'],
                'site' => $record['site'],
                'distant' => $record['distant'],
                'paquets' => $record['paquets'],
                'publie_le' => $record['publie_le'],
                'mis_a_jour_le' => $record['mis_a_jour_le'],
            ];
        }

        return JsonFile::encode([
            'source' => $sources->source,
            'page' => $sources->page,
            'date_source' => $sourceDate,
            'nombre' => \count($list),
            'nombres' => $counts,
            'serveurs' => $list,
        ]);
    }

    /**
     * @param list<Server> $servers
     * @param array<string, string> $categories nom du serveur => catégorie
     */
    public static function serversCsv(array $servers, array $categories): string
    {
        $lines = [self::csvLine(self::CSV_COLUMNS)];
        foreach (self::sorted($servers) as $server) {
            $lines[] = self::csvLine([
                $server->name,
                $server->title,
                $server->description,
                $server->version,
                $categories[$server->name] ?? Categories::OTHER,
                $server->repository,
                $server->website,
                self::flattenRemotes($server->remotes),
                self::flattenPackages($server->packages),
                $server->publishedOn,
                $server->updatedOn,
            ]);
        }

        return self::BOM . implode("\n", $lines) . "\n";
    }

    /**
     * En-tête seul de l'historique (fichier vide).
     *
     * @return non-empty-string
     */
    public static function historyHeader(): string
    {
        return self::BOM . self::csvLine(self::HISTORY_COLUMNS) . "\n";
    }

    /**
     * Historique allongé des nouveaux événements.
     *
     * @param list<Event> $events
     *
     * @throws SourceError
     */
    public static function appendHistory(string $current, array $events): string
    {
        if ($current === '') {
            $current = self::historyHeader();
        }
        if (!str_starts_with($current, self::historyHeader())) {
            throw new SourceError('data/historique.csv : en-tête inattendu, fichier non modifié.');
        }
        if (!str_ends_with($current, "\n")) {
            $current .= "\n";
        }
        foreach ($events as $event) {
            $current .= self::csvLine([$event['date'], $event['evenement'], $event['nom'], $event['categorie'], $event['detail']]) . "\n";
        }

        return $current;
    }

    /**
     * Noms (tels que publiés) des serveurs actuellement écartés d'après l'historique : dernier événement « ecarte ».
     *
     * @return list<string>
     *
     * @throws SourceError
     */
    public static function discardedNames(string $history): array
    {
        if ($history === '') {
            return [];
        }
        if (!str_starts_with($history, self::historyHeader())) {
            throw new SourceError('data/historique.csv : en-tête inattendu.');
        }
        $last = [];
        foreach (explode("\n", substr($history, \strlen(self::historyHeader()))) as $line) {
            if (trim($line) === '') {
                continue;
            }
            $cells = str_getcsv($line, self::SEPARATOR, '"', '');
            if (\count($cells) !== \count(self::HISTORY_COLUMNS) || !\is_string($cells[1]) || !\is_string($cells[2])) {
                throw new SourceError('data/historique.csv : ligne illisible « ' . mb_strimwidth($line, 0, 120, '…') . ' ».');
            }
            $last[$cells[2]] = $cells[1];
        }

        $discarded = [];
        foreach ($last as $name => $event) {
            if ($event === self::EVENT_DISCARDED) {
                $discarded[] = (string) $name;
            }
        }

        return $discarded;
    }

    /**
     * Serveurs de data/serveurs.json, avec leur catégorie publiée.
     *
     * @return array{servers: array<string, Server>, categories: array<string, string>}
     *
     * @throws SourceError
     */
    public static function readServers(string $content): array
    {
        $data = JsonFile::decode($content, 'data/' . self::SERVERS_JSON);
        $list = $data['serveurs'] ?? null;
        if (!\is_array($list) || !array_is_list($list)) {
            throw new SourceError('data/serveurs.json : liste « serveurs » absente.');
        }
        $servers = [];
        $categories = [];
        foreach ($list as $record) {
            if (!\is_array($record)) {
                throw new SourceError('data/serveurs.json : enregistrement invalide.');
            }
            $server = Server::fromArray($record, 'data/serveurs.json');
            $servers[$server->name] = $server;
            $categories[$server->name] = \is_string($record['categorie'] ?? null) ? $record['categorie'] : Categories::OTHER;
        }

        return ['servers' => $servers, 'categories' => $categories];
    }

    /**
     * @param list<Remote> $remotes
     */
    public static function flattenRemotes(array $remotes): string
    {
        $parts = [];
        foreach ($remotes as $remote) {
            $parts[] = $remote['type'] . ' ' . $remote['url'];
        }

        return implode(self::LIST_SEPARATOR, $parts);
    }

    /**
     * @param list<Package> $packages
     */
    public static function flattenPackages(array $packages): string
    {
        $parts = [];
        foreach ($packages as $package) {
            $parts[] = $package['registre'] . ' ' . $package['identifiant'];
        }

        return implode(self::LIST_SEPARATOR, $parts);
    }

    /**
     * Ligne CSV : valeur entre guillemets si elle contient le séparateur, un guillemet ou un retour à la ligne.
     *
     * @param list<string|null> $values
     */
    public static function csvLine(array $values): string
    {
        $cells = [];
        foreach ($values as $value) {
            $value ??= '';
            if (strpbrk($value, self::SEPARATOR . "\"\n\r") !== false) {
                $value = '"' . str_replace('"', '""', $value) . '"';
            }
            $cells[] = $value;
        }

        return implode(self::SEPARATOR, $cells);
    }

    /**
     * @param list<Server> $servers
     *
     * @return list<Server>
     */
    public static function sorted(array $servers): array
    {
        usort($servers, static fn(Server $a, Server $b): int => strcmp($a->name, $b->name));

        return $servers;
    }
}
