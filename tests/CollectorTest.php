<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Tests;

use PHPUnit\Framework\TestCase;
use PolygonLab\ServeursMcpFrance\Outils\Collector;
use PolygonLab\ServeursMcpFrance\Outils\Keywords;
use PolygonLab\ServeursMcpFrance\Outils\Server;
use PolygonLab\ServeursMcpFrance\Outils\SourceError;
use PolygonLab\ServeursMcpFrance\Outils\Sources;
use PolygonLab\ServeursMcpFrance\Tests\Fixtures\Registry;

/**
 * Collecte sur un faux registre (pages d'essai reproduisant les cas réels de l'API), sans appel réseau.
 */
final class CollectorTest extends TestCase
{
    public function testCollectsAllPagesAndKeepsActiveServersLinkedToFrance(): void
    {
        $registry = new Registry(Registry::fixturePages());
        $pauses = [];
        $result = self::collector($registry, $pauses)->collect();

        self::assertSame(2, $result['pages']);
        self::assertSame(11, $result['read']);
        self::assertSame(9, $result['active'], 'une entrée « deprecated », un nom en double');
        self::assertSame([
            'app.exemple/france-logistique',
            'com.exemple/france',
            'io.github.exemple/annuaire-siren',
            'io.github.exemple/contact-france',
            'io.github.exemple/donnees-publiques',
            'io.github.exemple/motinterdit-france',
            'io.github.exemple/passerelle-france',
            'org.exemple/horaires-sncf',
        ], array_map(static fn(Server $server): string => $server->name, $result['servers']));
        self::assertSame([1], $pauses, 'une pause d\'une seconde entre les deux pages');
    }

    public function testFollowsTheCursorAndAsksForTheLatestVersions(): void
    {
        $registry = new Registry(Registry::fixturePages());
        $pauses = [];
        self::collector($registry, $pauses)->collect();

        self::assertSame([
            'https://registry.modelcontextprotocol.io/v0/servers?version=latest&limit=100',
            'https://registry.modelcontextprotocol.io/v0/servers?version=latest&limit=100&cursor=io.github.exemple%2Fcontact-france%3A0.2.0',
        ], $registry->requests);
    }

    public function testKeepsTheLatestVersionOfAServerListedTwice(): void
    {
        $servers = self::byName(Registry::fixturePages());

        self::assertSame('1.1.0', $servers['io.github.exemple/annuaire-siren']->version, 'entrée « isLatest » gardée');
        self::assertSame([['registre' => 'npm', 'identifiant' => '@exemple/annuaire-siren']], $servers['io.github.exemple/annuaire-siren']->packages);
        self::assertSame('2026-10-20', $servers['io.github.exemple/annuaire-siren']->updatedOn);

        // Sans « isLatest » : la plus récemment mise à jour l'emporte, quel que soit l'ordre des pages.
        $old = Registry::entry('io.github.exemple/france-essai', '1.0.0', [], 'active', null, '2026-01-01T08:00:00Z');
        $new = Registry::entry('io.github.exemple/france-essai', '2.0.0', [], 'active', null, '2026-06-01T08:00:00Z');
        self::assertSame('2.0.0', self::byName([Registry::page([$new, $old])])['io.github.exemple/france-essai']->version);
        self::assertSame('2.0.0', self::byName([Registry::page([$old, $new])])['io.github.exemple/france-essai']->version);
    }

    public function testOptionalFieldsMayBeAbsentOrUnexpected(): void
    {
        $servers = self::byName(Registry::fixturePages());

        $minimal = $servers['com.exemple/france'];
        self::assertNull($minimal->title);
        self::assertNull($minimal->repository, 'serveur sans « repository »');
        self::assertNull($minimal->website);
        self::assertSame([], $minimal->remotes);
        self::assertSame([], $minimal->packages);
        self::assertSame('2026-08-03', $minimal->updatedOn, 'jour à l\'heure de Paris (23:30 UTC le 2 août)');

        $odd = $servers['io.github.exemple/donnees-publiques'];
        self::assertNull($odd->website, '« websiteUrl » numérique ignoré');
        self::assertSame([['type' => 'streamable-http', 'url' => 'https://mcp.example.com/annuaire']], $servers['io.github.exemple/annuaire-siren']->remotes, 'points d\'accès incomplets ignorés');
    }

    public function testStopsOnAnEntryWithoutAMandatoryField(): void
    {
        foreach ([
            'server.name' => ['server' => ['version' => '1.0.0', 'description' => 'France'], '_meta' => ['io.modelcontextprotocol.registry/official' => ['status' => 'active']]],
            'server.version' => ['server' => ['name' => 'io.github.exemple/sans-version'], '_meta' => ['io.modelcontextprotocol.registry/official' => ['status' => 'active']]],
            '_meta.io.modelcontextprotocol.registry/official.status' => ['server' => ['name' => 'io.github.exemple/sans-statut', 'version' => '1.0.0']],
        ] as $field => $entry) {
            try {
                $pauses = [];
                self::collector(new Registry([Registry::page([Registry::entry('com.exemple/france'), $entry])]), $pauses)->collect();
                self::fail('entrée sans ' . $field . ' acceptée');
            } catch (SourceError $error) {
                self::assertStringContainsString('Page 1, entrée n° 2 : champ obligatoire « ' . $field . ' » absent', $error->getMessage());
            }
        }
    }

    public function testCitesTheEntryWithoutName(): void
    {
        $this->expectException(SourceError::class);
        $this->expectExceptionMessage('"description":"France"');

        $pauses = [];
        self::collector(new Registry([Registry::page([['server' => ['version' => '1.0.0', 'description' => 'France']]])]), $pauses)->collect();
    }

    public function testStopsWhenThePageLimitIsReached(): void
    {
        $pages = [];
        for ($i = 1; $i <= 3; $i++) {
            $pages[] = Registry::page([Registry::entry('io.github.exemple/france-' . $i)], 'curseur-' . $i);
        }
        $this->expectException(SourceError::class);
        $this->expectExceptionMessage('Plafond de 3 pages atteint');

        $pauses = [];
        self::collector(new Registry($pages), $pauses, 3)->collect();
    }

    public function testStopsOnACursorLoop(): void
    {
        $this->expectException(SourceError::class);
        $this->expectExceptionMessage('déjà été servi');

        $pauses = [];
        self::collector(new Registry([
            Registry::page([Registry::entry('io.github.exemple/france-1')], 'boucle'),
            Registry::page([Registry::entry('io.github.exemple/france-2')], 'boucle'),
        ]), $pauses)->collect();
    }

    public function testStopsOnAnUnreadablePageOrAnUnreachableRegistry(): void
    {
        $pauses = [];
        foreach ([
            'réponse illisible' => new Registry(['<html>Erreur</html>']),
            'liste « servers » absente' => new Registry([['metadata' => []]]),
            'Registre injoignable' => new Registry([]),
        ] as $message => $registry) {
            try {
                self::collector($registry, $pauses)->collect();
                self::fail($message . ' accepté');
            } catch (SourceError $error) {
                self::assertStringContainsString($message, $error->getMessage());
            }
        }
    }

    public function testStopsWhenTheMaximumDurationIsExceeded(): void
    {
        $time = 0.0;
        $sources = Sources::load();
        $collector = new Collector(
            new Registry([Registry::page([Registry::entry('io.github.exemple/france-1')], 'suite'), Registry::page([])]),
            $sources,
            Keywords::load(),
            static function (int $seconds): void {},
            static function () use (&$time, $sources): float {
                $time += $sources->maxDurationSeconds + 1;

                return $time;
            },
        );
        $this->expectException(SourceError::class);
        $this->expectExceptionMessage('Durée maximale');

        $collector->collect();
    }

    /**
     * L'empreinte porte sur les données normalisées : ordre des pages, ordre des entrées et horodatages non repris
     * (statusChangedAt, heure de mise à jour dans la journée) ne la changent pas.
     */
    public function testNormalizedFileIgnoresOrderAndUnpublishedTimestamps(): void
    {
        $pages = Registry::fixturePages();
        $first = self::normalized($pages);

        $shuffled = $pages;
        $entries = array_merge(self::entries($shuffled[0]), self::entries($shuffled[1]));
        $entries = array_map(self::touched(...), array_reverse($entries));
        $second = self::normalized([Registry::page(\array_slice($entries, 0, 4), 'a'), Registry::page(\array_slice($entries, 4))]);

        self::assertSame(hash('sha256', $first), hash('sha256', $second));
        self::assertNotSame(hash('sha256', $first), hash('sha256', Collector::normalizedFile([], str_repeat('0', 64))));
    }

    public function testNormalizedFileIsReadBack(): void
    {
        $servers = self::byName(Registry::fixturePages());
        $file = Collector::normalizedFile(array_values($servers), str_repeat('a', 64));

        self::assertEquals(array_values($servers), Collector::readNormalizedFile($file));
    }

    public function testDayIsReadInParisTime(): void
    {
        self::assertSame('2026-07-10', Collector::day('2026-07-10T06:40:04.710706Z'));
        self::assertSame('2026-11-15', Collector::day('2026-11-14T23:30:00Z'));
        self::assertNull(Collector::day('now'));
        self::assertNull(Collector::day(null));
    }

    /**
     * @param list<int> $pauses
     */
    private static function collector(Registry $registry, array &$pauses, ?int $maxPages = null): Collector
    {
        $sources = Sources::load();
        if ($maxPages !== null) {
            $file = sys_get_temp_dir() . '/sources-essai-' . bin2hex(random_bytes(4)) . '.json';
            $data = json_decode((string) file_get_contents(Sources::FILE), true);
            self::assertIsArray($data);
            $data['pages_max'] = $maxPages;
            file_put_contents($file, (string) json_encode($data));
            $sources = Sources::load($file);
            unlink($file);
        }

        return new Collector($registry, $sources, Keywords::load(), static function (int $seconds) use (&$pauses): void {
            $pauses[] = $seconds;
        });
    }

    /**
     * @param list<array<mixed>> $pages
     *
     * @return array<string, Server>
     */
    private static function byName(array $pages): array
    {
        $pauses = [];
        $servers = [];
        foreach (self::collector(new Registry($pages), $pauses)->collect()['servers'] as $server) {
            $servers[$server->name] = $server;
        }

        return $servers;
    }

    /**
     * @param list<array<mixed>> $pages
     */
    private static function normalized(array $pages): string
    {
        $pauses = [];

        return Collector::normalizedFile(self::collector(new Registry($pages), $pauses)->collect()['servers'], str_repeat('a', 64));
    }

    /**
     * Entrée dont l'horodatage de changement de statut (non repris dans les données) a changé.
     *
     * @param array<mixed> $entry
     *
     * @return array<mixed>
     */
    private static function touched(array $entry): array
    {
        $meta = $entry['_meta'] ?? null;
        $official = \is_array($meta) ? ($meta['io.modelcontextprotocol.registry/official'] ?? null) : null;
        if (\is_array($meta) && \is_array($official)) {
            $official['statusChangedAt'] = '2026-11-14T06:00:00Z';
            $meta['io.modelcontextprotocol.registry/official'] = $official;
            $entry['_meta'] = $meta;
        }

        return $entry;
    }

    /**
     * @param array<mixed> $page
     *
     * @return list<array<mixed>>
     */
    private static function entries(array $page): array
    {
        $entries = [];
        foreach (\is_array($page['servers'] ?? null) ? $page['servers'] : [] as $entry) {
            if (\is_array($entry)) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }
}
