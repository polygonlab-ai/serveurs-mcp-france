<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Tests;

use PHPUnit\Framework\TestCase;
use PolygonLab\ServeursMcpFrance\Outils\Command;
use PolygonLab\ServeursMcpFrance\Outils\DataFiles;
use PolygonLab\ServeursMcpFrance\Outils\JsonFile;
use PolygonLab\ServeursMcpFrance\Outils\Readme;
use PolygonLab\ServeursMcpFrance\Tests\Fixtures\Workspace;

/**
 * La commande tools/actualiser.php, lancée dans un processus séparé, sur une collecte enregistrée (aucun appel
 * réseau).
 */
final class CommandTest extends TestCase
{
    private const SCRIPT = __DIR__ . '/../tools/actualiser.php';

    private Workspace $workspace;

    protected function setUp(): void
    {
        $this->workspace = new Workspace();
    }

    protected function tearDown(): void
    {
        $this->workspace->remove();
    }

    public function testCollectsAndUpdatesInOneGo(): void
    {
        $summary = $this->workspace->path . '/resume.json';
        [$code, $output] = $this->command(['--pages', Workspace::PAGES, '--resume', $summary, '--mots-interdits', $this->workspace->forbiddenWords]);

        self::assertSame(0, $code, $output);
        self::assertStringContainsString('Première collecte du registre du ', $output);
        self::assertStringContainsString(' : 6 serveurs', $output);
        self::assertStringContainsString('Fichiers de data/ et tableau du README réécrits.', $output);
        $list = JsonFile::decode($this->workspace->read(DataFiles::SERVERS_JSON), 'serveurs.json');
        self::assertSame(6, $list['nombre']);
        $decoded = JsonFile::read($summary, 'résumé');
        self::assertSame(1, $decoded['format']);
        self::assertTrue($decoded['change']);
        self::assertSame([], glob($this->workspace->data . '/.*.tmp') ?: [], 'aucun fichier temporaire laissé');

        // Tableau généré entre les balises du README, le reste du README inchangé.
        $readme = (string) file_get_contents($this->workspace->readme);
        $original = (string) file_get_contents(__DIR__ . '/../README.md');
        self::assertStringContainsString("### Entreprises et registres (1)\n\n| Serveur | Description | Version | Liens |", $readme);
        self::assertStringContainsString('| **Exemple Annuaire**<br>`io.github.exemple/annuaire-siren` | Recherche d\'entreprises par SIREN et SIRET. | 1.1.0 | [dépôt](https://github.com/exemple/annuaire-siren) |', $readme);
        self::assertSame(self::outside($original), self::outside($readme));

        // Deuxième passage identique : rien n'est réécrit.
        [$code, $output] = $this->command(['--pages', Workspace::PAGES, '--mots-interdits', $this->workspace->forbiddenWords]);
        self::assertSame(0, $code, $output);
        self::assertStringContainsString('Liste inchangée : aucun fichier modifié.', $output);
    }

    public function testCollectsThenUpdatesFromTheManifest(): void
    {
        $collect = $this->workspace->path . '/collecte';
        $manifest = $this->workspace->path . '/manifeste.json';
        $before = $this->workspace->read(DataFiles::SERVERS_JSON);

        [$code, $output] = $this->command(['--collecter', $collect, '--manifeste', $manifest, '--pages', Workspace::PAGES]);
        self::assertSame(0, $code, $output);
        self::assertStringContainsString('Collecte : 2 page(s), 11 entrée(s) lue(s), 9 serveur(s) actif(s), 8 retenu(s) par les mots-clés.', $output);
        self::assertSame($before, $this->workspace->read(DataFiles::SERVERS_JSON), 'collecte seule : data/ inchangé');

        $data = JsonFile::read($manifest, 'manifeste');
        self::assertIsArray($data['fichiers']);
        $entry = $data['fichiers'][0] ?? null;
        self::assertIsArray($entry);
        self::assertSame('registre', $entry['cle']);
        self::assertSame($collect . '/' . Command::COLLECTED_FILE, $entry['chemin']);
        self::assertSame(hash_file('sha256', $collect . '/' . Command::COLLECTED_FILE), $entry['sha256']);
        self::assertSame('https://registry.modelcontextprotocol.io/v0/servers?version=latest', $entry['url']);
        self::assertNull($entry['derniere_modification']);
        self::assertSame(2, $entry['pages']);

        [$code, $output] = $this->command(['--manifeste', $manifest, '--mots-interdits', $this->workspace->forbiddenWords]);
        self::assertSame(0, $code, $output);
        $source = JsonFile::decode($this->workspace->read(DataFiles::SOURCE_JSON), 'source.json');
        self::assertSame($entry['sha256'], self::firstFile($source)['sha256'], 'empreinte de la collecte reprise dans data/source.json');
    }

    public function testRefusesATamperedCollectionAndWritesNothing(): void
    {
        $collect = $this->workspace->path . '/collecte';
        $manifest = $this->workspace->path . '/manifeste.json';
        $this->command(['--collecter', $collect, '--manifeste', $manifest, '--pages', Workspace::PAGES]);
        file_put_contents($collect . '/' . Command::COLLECTED_FILE, "\n", FILE_APPEND);
        $before = $this->workspace->read(DataFiles::SERVERS_JSON);

        [$code, $output] = $this->command(['--manifeste', $manifest]);

        self::assertSame(1, $code);
        self::assertStringContainsString('empreinte SHA-256 différente', $output);
        self::assertStringContainsString('Aucun fichier n\'a été écrit.', $output);
        self::assertSame($before, $this->workspace->read(DataFiles::SERVERS_JSON));
    }

    public function testRefusesAnIncompleteCollectionAndWritesNothing(): void
    {
        $pages = $this->workspace->pages();
        unlink($pages . '/page-0002.json');
        $before = (string) file_get_contents($this->workspace->readme);

        [$code, $output] = $this->command(['--pages', $pages]);

        self::assertSame(1, $code);
        self::assertStringContainsString('Page enregistrée absente : page-0002.json', $output);
        self::assertSame($before, (string) file_get_contents($this->workspace->readme));
    }

    public function testRefusesAReadmeWithoutMarkers(): void
    {
        file_put_contents($this->workspace->readme, "# Essai\n\nSans balises.\n");
        $before = $this->workspace->read(DataFiles::SERVERS_JSON);

        [$code, $output] = $this->command(['--pages', Workspace::PAGES]);

        self::assertSame(1, $code);
        self::assertStringContainsString(Readme::START, $output);
        self::assertSame($before, $this->workspace->read(DataFiles::SERVERS_JSON), 'données inchangées quand le README est refusé');
    }

    public function testRecordsTheCollectionForALaterReplay(): void
    {
        $record = $this->workspace->path . '/enregistrement';
        [$code, $output] = $this->command(['--pages', Workspace::PAGES, '--enregistrer', $record, '--collecter', $this->workspace->path . '/c', '--manifeste', $this->workspace->path . '/m.json']);

        self::assertSame(0, $code, $output);
        self::assertSame(['page-0001.json', 'page-0002.json'], array_map('basename', glob($record . '/page-*.json') ?: []));
        self::assertSame(file_get_contents(Workspace::PAGES . '/page-0002.json'), file_get_contents($record . '/page-0002.json'));
    }

    public function testUsageErrors(): void
    {
        foreach ([
            [['--inconnu'], 'Argument non reconnu : --inconnu.'],
            [['--collecter', '/tmp/x'], '--collecter demande aussi --manifeste'],
            [['--resume'], 'Valeur manquante après --resume.'],
            [['--donnees', '/dossier/introuvable'], 'Dossier des données introuvable'],
        ] as [$arguments, $message]) {
            [$code, $output] = $this->command($arguments, false);
            self::assertSame(2, $code, $output);
            self::assertStringContainsString($message, $output);
        }
        [$code, $output] = $this->command(['--aide'], false);
        self::assertSame(0, $code);
        self::assertStringContainsString('Usage : php tools/actualiser.php [options]', $output);
    }

    /**
     * @param list<string> $arguments
     *
     * @return array{0: int, 1: string} code de sortie, sorties standard et d'erreur réunies
     */
    private function command(array $arguments, bool $workspace = true): array
    {
        if ($workspace) {
            $arguments = array_merge($arguments, ['--donnees', $this->workspace->data, '--readme', $this->workspace->readme]);
        }
        $process = proc_open(array_merge([PHP_BINARY, self::SCRIPT], $arguments), [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        self::assertIsResource($process);
        $output = (string) stream_get_contents($pipes[1]);
        $errors = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return [proc_close($process), $output . $errors];
    }

    /**
     * README sans la partie générée entre les balises.
     */
    private static function outside(string $readme): string
    {
        return (string) preg_replace('/' . preg_quote(Readme::START, '/') . '.*?' . preg_quote(Readme::END, '/') . '/s', '', $readme);
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
