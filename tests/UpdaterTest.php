<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Tests;

use PHPUnit\Framework\TestCase;
use PolygonLab\ServeursMcpFrance\Outils\Categories;
use PolygonLab\ServeursMcpFrance\Outils\Collector;
use PolygonLab\ServeursMcpFrance\Outils\DataFiles;
use PolygonLab\ServeursMcpFrance\Outils\Exclusions;
use PolygonLab\ServeursMcpFrance\Outils\ForbiddenWords;
use PolygonLab\ServeursMcpFrance\Outils\JsonFile;
use PolygonLab\ServeursMcpFrance\Outils\Keywords;
use PolygonLab\ServeursMcpFrance\Outils\Screening;
use PolygonLab\ServeursMcpFrance\Outils\Server;
use PolygonLab\ServeursMcpFrance\Outils\SourceError;
use PolygonLab\ServeursMcpFrance\Outils\Sources;
use PolygonLab\ServeursMcpFrance\Outils\Updater;
use PolygonLab\ServeursMcpFrance\Tests\Fixtures\Registry;
use PolygonLab\ServeursMcpFrance\Tests\Fixtures\Workspace;

/**
 * Comparaison avec la version en place, historique, catégories et résumé (garde-fous du script d'actualisation).
 */
final class UpdaterTest extends TestCase
{
    private Workspace $workspace;

    protected function setUp(): void
    {
        $this->workspace = new Workspace();
    }

    protected function tearDown(): void
    {
        $this->workspace->remove();
    }

    public function testFirstReadPublishesTheWholeList(): void
    {
        $result = $this->updateWith(Registry::fixturePages(), '2026-11-16T06:40:00+01:00');

        self::assertTrue($result['changed']);
        $summary = $result['summary'];
        self::assertSame('Première collecte du registre du 16/11/2026 : 6 serveurs', $summary['titre']);
        self::assertSame(0, $summary['avant']);
        self::assertSame(6, $summary['apres']);
        self::assertSame(0, $summary['retraits']);
        self::assertSame('serveur(s)', $summary['unite']);
        self::assertSame([['cle' => 'serveurs', 'libelle' => 'Serveurs MCP liés à la France', 'nombre' => 6]], $summary['listes']);

        $list = JsonFile::decode($result['files'][DataFiles::SERVERS_JSON], 'serveurs.json');
        self::assertSame('2026-11-16', $list['date_source']);
        self::assertSame(6, $list['nombre']);
        self::assertSame([
            'entreprises_registres' => 1,
            'emploi_social' => 0,
            'sante' => 0,
            'juridique_administration' => 0,
            'fiscalite_facturation' => 0,
            'transport_mobilite' => 2,
            'geographie_territoire' => 0,
            'donnees_publiques' => 2,
            'commerce_paiement' => 0,
            'autre' => 1,
        ], $list['nombres']);

        $history = $result['files'][DataFiles::HISTORY_CSV];
        self::assertStringContainsString("2026-11-16;entree;io.github.exemple/annuaire-siren;entreprises_registres;Entreprises et registres\n", $history);
        self::assertStringContainsString("2026-11-16;ecarte;io.github.exemple/passerelle-france;autre;écarté : adresse IPv4 (champ « description »)\n", $history);
        self::assertStringContainsString(';ecarte;[nom retiré ' . substr(hash('sha256', 'io.github.exemple/motinterdit-france'), 0, 8) . '];autre;écarté : mot interdit (champ « nom »)', $history);
        self::assertStringNotContainsString(Workspace::FORBIDDEN_WORD, $history . $result['files'][DataFiles::SERVERS_JSON] . $result['readme'] . (string) json_encode($summary));

        $source = JsonFile::decode($result['files'][DataFiles::SOURCE_JSON], 'source.json');
        self::assertSame('2026-11-16', $source['date_source']);
        self::assertSame('2026-11-16T06:40:00+01:00', $source['lu_le']);
        $file = self::firstFile($source);
        self::assertSame('https://registry.modelcontextprotocol.io/v0/servers?version=latest', $file['url']);
        self::assertSame(2, $file['pages']);
        self::assertSame(11, $file['entrees_lues']);

        self::assertStringContainsString('Adresse e-mail retirée de la description : io.github.exemple/contact-france', (string) json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public function testSameListIsUnchanged(): void
    {
        $this->store($this->updateWith(Registry::fixturePages(), '2026-11-16T06:40:00+01:00'));
        $result = $this->updateWith(Registry::fixturePages(), '2026-11-23T06:40:00+01:00');

        self::assertFalse($result['changed']);
        self::assertSame([], $result['files']);
        self::assertSame('Registre du 23/11/2026 : aucun changement', $result['summary']['titre']);
    }

    public function testEntriesExitsVersionsAndCategoryChanges(): void
    {
        $this->store($this->updateWith(Registry::fixturePages(), '2026-11-16T06:40:00+01:00'));

        $pages = Registry::fixturePages();
        $pages[] = Registry::page([
            Registry::entry('io.github.exemple/horaires-sncf-bis', '1.0.0', ['description' => 'Horaires SNCF, deuxième serveur.']),
            Registry::entry('org.exemple/horaires-sncf', '2026.11.1', ['title' => 'Horaires de train', 'description' => 'Horaires SNCF en temps réel.', 'remotes' => [['type' => 'sse', 'url' => 'https://mcp.example.com/sncf/sse']]], 'active', true, '2026-11-20T08:00:00Z'),
            Registry::entry('com.exemple/france', '1.0.0', ['description' => 'Jeux de données data.gouv.fr sur la France.'], 'active', true, '2026-11-21T08:00:00Z'),
        ]);
        $pages = self::withoutServer($pages, 'app.exemple/france-logistique');
        $result = $this->updateWith($pages, '2026-11-23T06:40:00+01:00');

        self::assertTrue($result['changed']);
        self::assertSame('Registre du 23/11/2026 : 1 entrée, 1 sortie, 1 nouvelle version, 2 autres modifications', $result['summary']['titre']);
        $history = $result['files'][DataFiles::HISTORY_CSV];
        self::assertStringContainsString("2026-11-23;entree;io.github.exemple/horaires-sncf-bis;transport_mobilite;Transport et mobilité\n", $history);
        self::assertStringContainsString("2026-11-23;sortie;app.exemple/france-logistique;transport_mobilite;absent du registre, plus actif ou sans mot-clé\n", $history);
        self::assertStringContainsString("2026-11-23;nouvelle_version;org.exemple/horaires-sncf;transport_mobilite;2026.11.0 -> 2026.11.1\n", $history);
        self::assertSame(1, $result['summary']['retraits']);
        self::assertSame(6, $result['summary']['avant']);
        self::assertSame(1, substr_count($history, ';ecarte;io.github.exemple/passerelle-france;'), 'serveur déjà écarté : pas noté une deuxième fois');
    }

    public function testCategoryChangeIsRecorded(): void
    {
        $this->store($this->updateWith(Registry::fixturePages(), '2026-11-16T06:40:00+01:00'));
        $pages = Registry::fixturePages();
        $pages[] = Registry::page([Registry::entry('com.exemple/france', '1.0.0', ['description' => 'Horaires de train pour la France.'], 'active', true, '2026-11-21T08:00:00Z')]);

        $result = $this->updateWith($pages, '2026-11-23T06:40:00+01:00');

        self::assertStringContainsString("2026-11-23;changement_categorie;com.exemple/france;transport_mobilite;donnees_publiques -> transport_mobilite\n", $result['files'][DataFiles::HISTORY_CSV]);
        $title = $result['summary']['titre'];
        self::assertIsString($title);
        self::assertStringContainsString('1 changement de catégorie', $title);
    }

    public function testAListedServerThatBecomesDiscardedCountsAsARemoval(): void
    {
        $this->store($this->updateWith(Registry::fixturePages(), '2026-11-16T06:40:00+01:00'));
        $pages = Registry::fixturePages();
        $pages[] = Registry::page([Registry::entry('com.exemple/france', '1.0.1', ['description' => 'France, voir ' . Workspace::FORBIDDEN_WORD . '.'], 'active', true, '2026-11-20T08:00:00Z')]);

        $result = $this->updateWith($pages, '2026-11-23T06:40:00+01:00');

        self::assertSame(1, $result['summary']['retraits']);
        self::assertStringContainsString("2026-11-23;ecarte;com.exemple/france;donnees_publiques;écarté : mot interdit (champ « description »)\n", $result['files'][DataFiles::HISTORY_CSV]);
    }

    public function testRefusesAnEmptyList(): void
    {
        $this->expectException(SourceError::class);
        $this->expectExceptionMessage('Liste vide');

        $this->updateWith([Registry::page([Registry::entry('io.github.exemple/motinterdit-france')])], '2026-11-16T06:40:00+01:00');
    }

    public function testEmptyFilesAreValidAndEmpty(): void
    {
        $files = Updater::emptyFiles(Sources::load(), Categories::load());

        self::assertSame(DataFiles::historyHeader(), $files[DataFiles::HISTORY_CSV]);
        $list = JsonFile::decode($files[DataFiles::SERVERS_JSON], 'serveurs.json');
        self::assertSame([], $list['serveurs']);
        self::assertNull($list['date_source']);
    }

    public function testHistoryWithAnUnexpectedHeaderIsRefused(): void
    {
        file_put_contents($this->workspace->data . '/' . DataFiles::HISTORY_CSV, "date;autre\n");
        $this->expectException(SourceError::class);
        $this->expectExceptionMessage('en-tête inattendu');

        $this->updateWith(Registry::fixturePages(), '2026-11-16T06:40:00+01:00');
    }

    /**
     * @param list<array<mixed>> $pages
     *
     * @return array{changed: bool, files: array<string, string>, readme: string, summary: array<string, mixed>}
     */
    private function updateWith(array $pages, string $now): array
    {
        $collected = (new Collector(new Registry(Registry::chain($pages)), Sources::load(), Keywords::load(), static function (int $seconds): void {}))->collect();
        $content = Collector::normalizedFile($collected['servers'], str_repeat('a', 64));
        $screening = new Screening(Exclusions::load(), ForbiddenWords::load($this->workspace->forbiddenWords));

        return (new Updater(Sources::load(), Categories::load(), $screening, $this->workspace->data))->run([
            'servers' => $collected['servers'],
            'sha256' => hash('sha256', $content),
            'size' => \strlen($content),
            'pages' => $collected['pages'],
            'read' => $collected['read'],
            'active' => $collected['active'],
            'url' => Sources::load()->listUrl(),
            'path' => '/essai/registre.json',
        ], new \DateTimeImmutable($now));
    }

    /**
     * @param array{changed: bool, files: array<string, string>, readme: string, summary: array<string, mixed>} $result
     */
    private function store(array $result): void
    {
        foreach ($result['files'] as $name => $content) {
            file_put_contents($this->workspace->data . '/' . $name, $content);
        }
    }

    /**
     * @param list<array<mixed>> $pages
     *
     * @return list<array<mixed>>
     */
    private static function withoutServer(array $pages, string $name): array
    {
        $result = [];
        foreach ($pages as $page) {
            $entries = \is_array($page['servers'] ?? null) ? $page['servers'] : [];
            $page['servers'] = array_values(array_filter($entries, static function (mixed $entry) use ($name): bool {
                $server = \is_array($entry) ? ($entry['server'] ?? null) : null;

                return !\is_array($server) || ($server['name'] ?? null) !== $name;
            }));
            $result[] = $page;
        }

        return $result;
    }

    /**
     * Un nom n'apparaît qu'une fois dans la collecte, même quand le registre le donne en plusieurs versions.
     */
    public function testServerNamesAreUnique(): void
    {
        $names = array_map(static fn(Server $server): string => $server->name, (new Collector(new Registry(Registry::fixturePages()), Sources::load(), Keywords::load(), static function (int $seconds): void {}))->collect()['servers']);

        self::assertSame($names, array_values(array_unique($names)));
    }

    /**
     * Première entrée de « fichiers » de data/source.json.
     *
     * @param array<string, mixed> $source
     *
     * @return array<mixed>
     */
    private static function firstFile(array $source): array
    {
        $files = $source['fichiers'] ?? null;
        $first = \is_array($files) ? ($files[0] ?? null) : null;
        self::assertIsArray($first);

        return $first;
    }
}
