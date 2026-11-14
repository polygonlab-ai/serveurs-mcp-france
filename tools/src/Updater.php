<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Compare la liste issue de la collecte à la version en place dans data/ et prépare les nouveaux fichiers, le
 * tableau du README et le résumé des changements.
 *
 * Rien n'est écrit ici : Updater::run() rend le contenu des fichiers (vide si la liste n'a pas changé) et le résumé,
 * repris par le journal des changements et le message d'information.
 *
 * Événements de l'historique : entrée, sortie, nouvelle version, changement de catégorie, écarté (mot interdit,
 * adresse IPv4… : voir Screening). Un serveur retiré par la liste d'exclusion sort de la liste (« sortie »).
 * Une liste vide arrête l'actualisation (SourceError, rien n'est écrit).
 *
 * @phpstan-import-type Event from DataFiles
 * @phpstan-import-type Discarded from Screening
 * @phpstan-import-type Excluded from Screening
 * @phpstan-type Collected array{servers: list<Server>, sha256: string, size: int, pages: int, read: int, active: int, url: string, path: string}
 * @phpstan-type Section array{titre: string, elements: list<string>}
 */
final class Updater
{
    public const SUMMARY_FORMAT = 1;
    public const REPOSITORY = 'serveurs-mcp-france';
    public const TIMEZONE = 'Europe/Paris';

    /** Unité des garde-fous du script d'actualisation (« 3 serveur(s) sur 10 disparaissent »). */
    public const UNIT = 'serveur(s)';

    public const LIST_KEY = 'serveurs';
    public const LIST_LABEL = 'Serveurs MCP liés à la France';

    public function __construct(
        private readonly Sources $sources,
        private readonly Categories $categories,
        private readonly Screening $screening,
        private readonly string $dataDirectory,
    ) {}

    /**
     * @param Collected $collected
     *
     * @return array{changed: bool, files: array<string, string>, readme: string, summary: array<string, mixed>}
     *
     * @throws SourceError
     */
    public function run(array $collected, \DateTimeImmutable $now): array
    {
        $now = $now->setTimezone(new \DateTimeZone(self::TIMEZONE));
        $today = $now->format('Y-m-d');
        $previousSource = $this->previousSource();
        $previous = DataFiles::readServers($this->read(DataFiles::SERVERS_JSON));
        $history = $this->read(DataFiles::HISTORY_CSV);
        $firstRead = $previous['servers'] === [] && ($previousSource['fichiers'] ?? []) === [];

        $screened = $this->screening->screen($collected['servers']);
        if ($screened['kept'] === []) {
            throw new SourceError(sprintf(
                'Liste vide : aucun serveur publiable parmi les %d retenus par les mots-clés (%d écarté(s), %d exclu(s)).',
                \count($collected['servers']),
                \count($screened['discarded']),
                \count($screened['excluded']),
            ));
        }
        $current = [];
        $assigned = [];
        foreach ($screened['kept'] as $server) {
            $current[$server->name] = $server;
            $assigned[$server->name] = $this->categories->classify($server);
        }
        ksort($current, SORT_STRING);

        [$events, $others, $exits, $touched] = $this->compare($previous, $current, $assigned, $screened['discarded'], $screened['excluded'], DataFiles::discardedNames($history), $today);
        $changed = $firstRead || $events !== [] || $others !== [];

        $summary = $this->summary($firstRead, $changed, \count($previous['servers']), $current, $assigned, $events, $others, $exits, $touched, $screened, $today, $now->format(DATE_ATOM));
        if (!$changed) {
            return ['changed' => false, 'files' => [], 'readme' => '', 'summary' => $summary];
        }

        $list = array_values($current);

        return [
            'changed' => true,
            'files' => [
                DataFiles::SERVERS_JSON => DataFiles::serversJson($this->sources, $today, $list, $assigned, $this->categories->keys()),
                DataFiles::SERVERS_CSV => DataFiles::serversCsv($list, $assigned),
                DataFiles::HISTORY_CSV => DataFiles::appendHistory($history, $events),
                DataFiles::SOURCE_JSON => JsonFile::encode([
                    'source' => $this->sources->source,
                    'page' => $this->sources->page,
                    'lu_le' => $now->format(DATE_ATOM),
                    'date_source' => $today,
                    'fichiers' => [[
                        'cle' => Sources::KEY,
                        'url' => $collected['url'],
                        'sha256' => $collected['sha256'],
                        'taille' => $collected['size'],
                        'pages' => $collected['pages'],
                        'entrees_lues' => $collected['read'],
                        'serveurs_actifs' => $collected['active'],
                        'serveurs_retenus' => \count($collected['servers']),
                    ]],
                ]),
            ],
            'readme' => Readme::block($today, $list, $assigned, $this->categories),
            'summary' => $summary,
        ];
    }

    /**
     * Fichiers vides mais valides, pour un dépôt neuf.
     *
     * @return array<string, string>
     */
    public static function emptyFiles(Sources $sources, Categories $categories): array
    {
        return [
            DataFiles::SERVERS_JSON => DataFiles::serversJson($sources, null, [], [], $categories->keys()),
            DataFiles::SERVERS_CSV => DataFiles::serversCsv([], []),
            DataFiles::HISTORY_CSV => DataFiles::historyHeader(),
            DataFiles::SOURCE_JSON => JsonFile::encode([
                'source' => $sources->source,
                'page' => $sources->page,
                'lu_le' => null,
                'date_source' => null,
                'fichiers' => [],
            ]),
        ];
    }

    public static function plural(int $count, string $singular, string $plural): string
    {
        return $count . ' ' . ($count > 1 ? $plural : $singular);
    }

    /**
     * @param array{servers: array<string, Server>, categories: array<string, string>} $previous
     * @param array<string, Server> $current
     * @param array<string, string> $assigned
     * @param list<Discarded> $discarded
     * @param list<Excluded> $excluded
     * @param list<string> $alreadyDiscarded
     *
     * @return array{0: list<Event>, 1: list<string>, 2: int, 3: list<string>} événements, autres modifications,
     *         retraits de la liste, serveurs entrés ou modifiés
     */
    private function compare(array $previous, array $current, array $assigned, array $discarded, array $excluded, array $alreadyDiscarded, string $date): array
    {
        $events = [];
        $others = [];
        $exits = 0;
        $touched = [];
        foreach ($current as $name => $server) {
            $name = (string) $name;
            $category = $assigned[$name];
            $before = $previous['servers'][$name] ?? null;
            if ($before === null) {
                $events[] = self::event($date, DataFiles::EVENT_ENTRY, $name, $category, $this->categories->label($category));
                $touched[] = $name;
                continue;
            }
            if ($before->version !== $server->version) {
                $events[] = self::event($date, DataFiles::EVENT_VERSION, $name, $category, $before->version . ' -> ' . $server->version);
            }
            $beforeCategory = $previous['categories'][$name] ?? Categories::OTHER;
            if ($beforeCategory !== $category) {
                $events[] = self::event($date, DataFiles::EVENT_CATEGORY, $name, $category, $beforeCategory . ' -> ' . $category);
            }
            $fields = [];
            $old = $before->toArray();
            foreach ($server->toArray() as $field => $value) {
                if ($field !== 'version' && $old[$field] !== $value) {
                    $fields[] = $field;
                }
            }
            if ($fields !== []) {
                $others[] = $name . ' : ' . implode(', ', $fields);
            }
            if ($fields !== [] || $before->version !== $server->version) {
                $touched[] = $name;
            }
        }

        $discardedByName = [];
        foreach ($discarded as $item) {
            $discardedByName[$item['server']->name] = $item;
        }
        $excludedByName = [];
        foreach ($excluded as $item) {
            $excludedByName[$item['server']->name] = $item['reason'];
        }
        $removed = array_diff_key($previous['servers'], $current);
        ksort($removed, SORT_STRING);
        foreach (array_keys($removed) as $name) {
            $name = (string) $name;
            $exits++;
            $category = $previous['categories'][$name] ?? Categories::OTHER;
            if (isset($discardedByName[$name])) {
                $events[] = self::event($date, DataFiles::EVENT_DISCARDED, $discardedByName[$name]['name'], $category, 'écarté : ' . $discardedByName[$name]['reason']);
            } elseif (isset($excludedByName[$name])) {
                $events[] = self::event($date, DataFiles::EVENT_EXIT, $name, $category, 'liste d\'exclusion : ' . $excludedByName[$name]);
            } else {
                $events[] = self::event($date, DataFiles::EVENT_EXIT, $name, $category, 'absent du registre, plus actif ou sans mot-clé');
            }
        }
        foreach ($discardedByName as $name => $item) {
            if (!isset($previous['servers'][(string) $name]) && !\in_array($item['name'], $alreadyDiscarded, true)) {
                $events[] = self::event($date, DataFiles::EVENT_DISCARDED, $item['name'], $this->categories->classify($item['server']), 'écarté : ' . $item['reason']);
            }
        }

        return [$events, $others, $exits, $touched];
    }

    /**
     * @return Event
     */
    private static function event(string $date, string $type, string $name, string $category, string $detail): array
    {
        return ['date' => $date, 'evenement' => $type, 'nom' => $name, 'categorie' => $category, 'detail' => $detail];
    }

    /**
     * Résumé des changements : compteurs (garde-fous), sections (journal des changements, message), titre.
     *
     * @param array<string, Server> $current
     * @param array<string, string> $assigned
     * @param list<Event> $events
     * @param list<string> $others
     * @param list<string> $touched
     * @param array{kept: list<Server>, discarded: list<Discarded>, excluded: list<Excluded>, emails: list<string>} $screened
     *
     * @return array<string, mixed>
     */
    private function summary(bool $firstRead, bool $changed, int $before, array $current, array $assigned, array $events, array $others, int $exits, array $touched, array $screened, string $sourceDate, string $readAt): array
    {
        /** @var array<string, list<string>> $byType */
        $byType = array_fill_keys([DataFiles::EVENT_ENTRY, DataFiles::EVENT_EXIT, DataFiles::EVENT_VERSION, DataFiles::EVENT_CATEGORY, DataFiles::EVENT_DISCARDED], []);
        foreach ($events as $event) {
            $line = $event['nom'];
            if ($event['evenement'] === DataFiles::EVENT_ENTRY) {
                $line .= ' (' . $event['detail'] . ')';
            } elseif ($event['evenement'] !== DataFiles::EVENT_EXIT || $event['detail'] !== '') {
                $line .= ' : ' . $event['detail'];
            }
            $byType[$event['evenement']][] = $line;
        }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $sourceDate);
        $displayDate = $date === false ? $sourceDate : $date->format('d/m/Y');

        $sections = [];
        if ($firstRead) {
            $title = sprintf('Première collecte du registre du %s : %s', $displayDate, self::plural(\count($current), 'serveur', 'serveurs'));
            if ($byType[DataFiles::EVENT_DISCARDED] !== []) {
                $sections[] = ['titre' => 'Écartés automatiquement', 'elements' => $byType[DataFiles::EVENT_DISCARDED]];
            }
        } else {
            $parts = [];
            foreach ([
                DataFiles::EVENT_ENTRY => ['entrée', 'entrées', 'Entrées'],
                DataFiles::EVENT_EXIT => ['sortie', 'sorties', 'Sorties'],
                DataFiles::EVENT_VERSION => ['nouvelle version', 'nouvelles versions', 'Nouvelles versions'],
                DataFiles::EVENT_CATEGORY => ['changement de catégorie', 'changements de catégorie', 'Changements de catégorie'],
                DataFiles::EVENT_DISCARDED => ['écarté', 'écartés', 'Écartés automatiquement'],
            ] as $type => [$singular, $pluralLabel, $sectionTitle]) {
                if ($byType[$type] !== []) {
                    $parts[] = self::plural(\count($byType[$type]), $singular, $pluralLabel);
                    $sections[] = ['titre' => $sectionTitle, 'elements' => $byType[$type]];
                }
            }
            if ($others !== []) {
                $parts[] = self::plural(\count($others), 'autre modification', 'autres modifications');
                $sections[] = ['titre' => 'Autres modifications', 'elements' => $others];
            }
            $title = $changed
                ? sprintf('Registre du %s : %s', $displayDate, implode(', ', $parts))
                : sprintf('Registre du %s : aucun changement', $displayDate);
        }

        $remarks = [];
        foreach ($screened['emails'] as $name) {
            if (isset($current[$name]) && ($firstRead || \in_array($name, $touched, true))) {
                $remarks[] = 'Adresse e-mail retirée de la description : ' . $name;
            }
        }
        foreach ($screened['excluded'] as $item) {
            $remarks[] = 'Exclu (liste d\'exclusion) : ' . $item['server']->name . ' — ' . $item['reason'];
        }
        if ($remarks !== []) {
            $sections[] = ['titre' => 'Remarques', 'elements' => $remarks];
        }

        $counts = array_count_values($assigned);
        $lists = [self::LIST_LABEL . ' : ' . self::plural(\count($current), 'serveur', 'serveurs')];
        foreach ($this->categories->keys() as $key) {
            if (($counts[$key] ?? 0) > 0) {
                $lists[] = $this->categories->label($key) . ' : ' . self::plural($counts[$key], 'serveur', 'serveurs');
            }
        }
        $sections[] = ['titre' => 'Listes', 'elements' => $lists];

        $text = [$title];
        foreach ($sections as $section) {
            $text[] = '';
            $text[] = $section['titre'];
            foreach ($section['elements'] as $element) {
                $text[] = '- ' . $element;
            }
        }

        return [
            'format' => self::SUMMARY_FORMAT,
            'depot' => self::REPOSITORY,
            'change' => $changed,
            'premiere_lecture' => $firstRead,
            'date_source' => $sourceDate,
            'lu_le' => $readAt,
            'avant' => $before,
            'apres' => \count($current),
            'retraits' => $exits,
            'unite' => self::UNIT,
            'listes' => [['cle' => self::LIST_KEY, 'libelle' => self::LIST_LABEL, 'nombre' => \count($current)]],
            'titre' => $title,
            'sections' => $sections,
            'texte' => implode("\n", $text),
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SourceError
     */
    private function previousSource(): array
    {
        $content = $this->read(DataFiles::SOURCE_JSON);

        return $content === '' ? [] : JsonFile::decode($content, 'data/' . DataFiles::SOURCE_JSON);
    }

    private function read(string $name): string
    {
        $path = $this->dataDirectory . '/' . $name;
        if (!is_file($path)) {
            return $name === DataFiles::SERVERS_JSON ? '{"serveurs": []}' : '';
        }

        return (string) file_get_contents($path);
    }
}
