<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Tests;

use PHPUnit\Framework\TestCase;
use PolygonLab\ServeursMcpFrance\Outils\JsonFile;
use PolygonLab\ServeursMcpFrance\Outils\Readme;

/**
 * Le démarrage rapide du README est le script d'exemple, et il produit la sortie annoncée (lancé ici sur une
 * copie locale fabriquée, sans appel réseau) ; les mots-clés et les balises du README sont à jour.
 */
final class ReadmeTest extends TestCase
{
    private const ROOT = __DIR__ . '/..';

    private const EXAMPLE = self::ROOT . '/examples/lire-la-liste.php';

    public function testQuickStartCodeIsTheExampleScript(): void
    {
        self::assertSame(rtrim((string) file_get_contents(self::EXAMPLE), "\n"), self::block('php'));
    }

    public function testQuickStartOutputIsTheExpectedOutput(): void
    {
        $copy = sys_get_temp_dir() . '/serveurs-exemple-' . bin2hex(random_bytes(6)) . '.json';
        file_put_contents($copy, json_encode([
            'source' => 'Registre MCP officiel, registry.modelcontextprotocol.io',
            'page' => 'https://registry.modelcontextprotocol.io',
            'date_source' => '2026-11-16',
            'nombre' => 2,
            'nombres' => ['entreprises_registres' => 1, 'donnees_publiques' => 1],
            'serveurs' => [
                self::exampleServer('io.github.exemple/annuaire', 'Exemple Annuaire', 'entreprises_registres', [['type' => 'streamable-http', 'url' => 'https://mcp.example.com/annuaire']]),
                self::exampleServer('io.github.exemple/donnees', null, 'donnees_publiques', []),
            ],
        ]));
        try {
            $process = proc_open([PHP_BINARY, self::EXAMPLE, $copy], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
            self::assertIsResource($process);
            $output = (string) stream_get_contents($pipes[1]);
            $errors = (string) stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            self::assertSame(0, proc_close($process), $errors);
        } finally {
            unlink($copy);
        }

        self::assertSame(self::block('text'), rtrim($output, "\n"));
    }

    public function testCurlExampleTargetsThePublishedFiles(): void
    {
        $readme = self::readme();

        foreach (['serveurs.json', 'serveurs.csv', 'historique.csv', 'source.json'] as $file) {
            self::assertStringContainsString('https://raw.githubusercontent.com/polygonlab-ai/serveurs-mcp-france/main/data/' . $file, $readme);
            self::assertFileExists(self::ROOT . '/data/' . $file);
        }
    }

    public function testListsTheKeywordsOfTheConfiguration(): void
    {
        $keywords = JsonFile::read(self::ROOT . '/tools/mots-cles.json', 'mots-cles.json')['mots_cles'];
        self::assertIsArray($keywords);
        $start = strpos(self::readme(), 'Liste actuelle : ');
        self::assertNotFalse($start, 'liste des mots-clés absente du README');
        $end = strpos(self::readme(), "\n4. ", $start);
        self::assertNotFalse($end);
        preg_match_all('/`([^`]+)`/', substr(self::readme(), $start, $end - $start), $matches);

        self::assertSame($keywords, $matches[1]);
    }

    public function testStatesThatServersAreNotTested(): void
    {
        self::assertStringContainsString('Liste factuelle reprise du registre officiel : PolygonLab ne teste ni ne recommande ces serveurs.', self::readme());
        self::assertSame(1, substr_count(self::readme(), Readme::START));
        self::assertSame(1, substr_count(self::readme(), Readme::END));
    }

    /**
     * Serveur d'une liste d'exemple, au format de data/serveurs.json.
     *
     * @param list<array{type: string, url: string}> $remotes
     *
     * @return array<string, mixed>
     */
    private static function exampleServer(string $name, ?string $title, string $category, array $remotes): array
    {
        return [
            'nom' => $name,
            'titre' => $title,
            'description' => 'Serveur d\'exemple.',
            'version' => '1.0.0',
            'categorie' => $category,
            'depot' => null,
            'site' => null,
            'distant' => $remotes,
            'paquets' => [],
            'publie_le' => '2026-11-01',
            'mis_a_jour_le' => '2026-11-01',
        ];
    }

    private static function readme(): string
    {
        return (string) file_get_contents(self::ROOT . '/README.md');
    }

    /**
     * Premier bloc de code du langage donné dans la section « Démarrage rapide ».
     */
    private static function block(string $language): string
    {
        $readme = self::readme();
        $start = strpos($readme, "## Démarrage rapide\n");
        self::assertNotFalse($start, 'section « Démarrage rapide » absente du README');

        $pattern = '/```' . preg_quote($language, '/') . '\n(.*?)\n```/s';
        self::assertSame(1, preg_match($pattern, substr($readme, $start), $matches), 'bloc ' . $language . ' absent');

        return $matches[1];
    }
}
