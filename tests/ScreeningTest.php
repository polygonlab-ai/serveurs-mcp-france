<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Tests;

use PHPUnit\Framework\TestCase;
use PolygonLab\ServeursMcpFrance\Outils\Exclusions;
use PolygonLab\ServeursMcpFrance\Outils\ForbiddenWords;
use PolygonLab\ServeursMcpFrance\Outils\Screening;
use PolygonLab\ServeursMcpFrance\Outils\Server;
use PolygonLab\ServeursMcpFrance\Outils\SourceError;

/**
 * Tri avant publication : exclusions, adresses e-mail retirées, serveurs écartés automatiquement. Les valeurs
 * refusées (adresse IPv4, jeton) sont fabriquées ici, jamais écrites telles quelles dans les fichiers du dépôt.
 */
final class ScreeningTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/tri-essai-' . bin2hex(random_bytes(6));
        mkdir($this->directory, 0700);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . '/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->directory);
    }

    public function testReplacesEmailAddressesInTheDescription(): void
    {
        $result = $this->screening()->screen([self::server('io.github.exemple/contact-france', 'Écrire à contact@example.com ou à Support.Equipe@' . 'exemple.fr.')]);

        self::assertCount(1, $result['kept']);
        self::assertSame('Écrire à [adresse retirée] ou à [adresse retirée].', $result['kept'][0]->description);
        self::assertSame(['io.github.exemple/contact-france'], $result['emails']);
    }

    public function testDiscardsAServerWithAnIpv4Address(): void
    {
        $address = implode('.', [10, 20, 30, 40]);
        $result = $this->screening()->screen([
            self::server('io.github.exemple/passerelle-france', 'Serveur sur http://' . $address . ':8080.'),
            self::server('io.github.exemple/version-france', 'France.', implode('.', [1, 0, 0, 0])),
            self::server('io.github.exemple/distant-france', 'France.', '1.0.0', 'https://' . $address . '/mcp'),
            self::server('io.github.exemple/france', 'Version 1.2.3 et 2026.11.14 ne sont pas des adresses.'),
        ]);

        self::assertSame(['io.github.exemple/france'], array_map(static fn(Server $s): string => $s->name, $result['kept']));
        self::assertSame([
            'adresse IPv4 (champ « description »)',
            'adresse IPv4 (champ « version »)',
            'adresse IPv4 (champ « depot »)',
        ], array_column($result['discarded'], 'reason'));
    }

    public function testDiscardsAServerWithAForbiddenWordUnlessTolerated(): void
    {
        $result = $this->screening()->screen([
            self::server('io.github.exemple/france', 'Outil MotInterdit pour la France.'),
            self::server('io.github.exemple/france-2', 'Outil motinterdit-autorise pour la France.'),
            self::server('io.github.exemple/france-3', 'Outil motinterdit-autorisez pour la France.'),
        ]);

        self::assertSame(['io.github.exemple/france-2'], array_map(static fn(Server $s): string => $s->name, $result['kept']), 'écriture tolérée exacte et délimitée seulement');
        self::assertSame('mot interdit (champ « description »)', $result['discarded'][0]['reason']);
        self::assertSame('io.github.exemple/france', $result['discarded'][0]['name'], 'nom publiable : cité tel quel');
    }

    public function testHidesTheNameWhenTheNameItselfIsRefused(): void
    {
        $result = $this->screening()->screen([self::server('io.github.exemple/motinterdit-france', 'France.')]);

        self::assertSame([], $result['kept']);
        self::assertSame('mot interdit (champ « nom »)', $result['discarded'][0]['reason']);
        self::assertSame('[nom retiré ' . substr(hash('sha256', 'io.github.exemple/motinterdit-france'), 0, 8) . ']', $result['discarded'][0]['name']);
    }

    public function testDiscardsSecretsTemplatesAndEmailAddressesOutsideTheDescription(): void
    {
        $result = $this->screening()->screen([
            self::server('io.github.exemple/cle-france', 'France, clé ' . 'sk-' . str_repeat('a1', 12) . '.'),
            self::server('io.github.exemple/gabarit-france', 'France ' . str_repeat('{', 2) . 'nom' . str_repeat('}', 2) . '.'),
            self::server('io.github.exemple/site-france', 'France.', '1.0.0', 'https://www.example.com/contact@' . 'exemple.fr'),
        ]);

        self::assertSame([], $result['kept']);
        self::assertSame([
            'motif de clé d\'API ou de jeton (champ « description »)',
            'texte entre doubles accolades (champ « description »)',
            'adresse e-mail (champ « depot »)',
        ], array_column($result['discarded'], 'reason'));
    }

    public function testAppliesTheExclusionList(): void
    {
        file_put_contents($this->directory . '/exclusions.json', (string) json_encode(['exclusions' => [
            ['nom' => 'com.exemple/tour-de-france', 'raison' => 'Course cycliste, sans lien avec les services publics ou les entreprises.', 'date' => '2026-11-16'],
        ]]));
        $screening = new Screening(Exclusions::load($this->directory . '/exclusions.json'), ForbiddenWords::none());
        $result = $screening->screen([self::server('com.exemple/tour-de-france', 'Tour de France.'), self::server('com.exemple/france', 'France.')]);

        self::assertSame(['com.exemple/france'], array_map(static fn(Server $s): string => $s->name, $result['kept']));
        self::assertSame('com.exemple/tour-de-france', $result['excluded'][0]['server']->name);
        self::assertStringStartsWith('Course cycliste', $result['excluded'][0]['reason']);
    }

    public function testRefusesAnInvalidExclusionList(): void
    {
        foreach ([
            ['exclusions' => [['nom' => 'a', 'raison' => 'b']]],
            ['exclusions' => [['nom' => 'a', 'raison' => 'b', 'date' => '16/11/2026']]],
            ['exclusions' => [['nom' => 'a', 'raison' => 'b', 'date' => '2026-11-16'], ['nom' => 'a', 'raison' => 'c', 'date' => '2026-11-16']]],
            ['autre' => []],
        ] as $index => $data) {
            file_put_contents($this->directory . '/exclusions.json', (string) json_encode($data));
            try {
                Exclusions::load($this->directory . '/exclusions.json');
                self::fail('liste d\'exclusion n° ' . $index . ' acceptée');
            } catch (SourceError $error) {
                self::assertStringContainsString('tools/exclusions.json', $error->getMessage());
            }
        }
    }

    public function testRefusesAnInvalidForbiddenWordsFile(): void
    {
        file_put_contents($this->directory . '/mots.json', (string) json_encode(['mots' => [['mot' => 'abc', 'tolerees' => ['xyz build']]]]));
        $this->expectException(SourceError::class);
        $this->expectExceptionMessage('ne contient pas ce mot');

        ForbiddenWords::load($this->directory . '/mots.json');
    }

    public function testWithoutForbiddenWordsNothingIsDiscardedForWords(): void
    {
        $screening = new Screening(Exclusions::load(), ForbiddenWords::none());

        self::assertCount(1, $screening->screen([self::server('io.github.exemple/france', 'Outil motinterdit pour la France.')])['kept']);
        self::assertSame(0, ForbiddenWords::none()->count());
    }

    private function screening(): Screening
    {
        file_put_contents($this->directory . '/mots.json', (string) json_encode(['mots' => [['mot' => 'motinterdit', 'tolerees' => ['motinterdit-autorise']]]]));

        return new Screening(Exclusions::load(), ForbiddenWords::load($this->directory . '/mots.json'));
    }

    private static function server(string $name, ?string $description, string $version = '1.0.0', ?string $repository = null): Server
    {
        return new Server($name, null, $description, $version, $repository, null, [], [], '2026-11-01', '2026-11-01');
    }
}
