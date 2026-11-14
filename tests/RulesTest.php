<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Tests;

use PHPUnit\Framework\TestCase;
use PolygonLab\ServeursMcpFrance\Outils\Categories;
use PolygonLab\ServeursMcpFrance\Outils\Keywords;
use PolygonLab\ServeursMcpFrance\Outils\Server;
use PolygonLab\ServeursMcpFrance\Outils\SourceError;
use PolygonLab\ServeursMcpFrance\Outils\Sources;
use PolygonLab\ServeursMcpFrance\Outils\Text;

/**
 * Règles de sélection : configuration de tools/, mots-clés en mots entiers sans casse ni accents, catégories,
 * échappement du tableau du README.
 */
final class RulesTest extends TestCase
{
    public function testSourcesDeclareTheOfficialRegistry(): void
    {
        $sources = Sources::load();

        self::assertSame('https://registry.modelcontextprotocol.io/v0/servers?version=latest', $sources->listUrl());
        self::assertSame('https://registry.modelcontextprotocol.io/v0/servers?version=latest&limit=100&cursor=a%2Fb%3A1.0.0', $sources->pageUrl('a/b:1.0.0'));
        self::assertSame(1, $sources->pauseSeconds);
        self::assertSame(3, $sources->attempts);
        self::assertGreaterThan(640, $sources->maxPages, 'le registre comptait 640 pages le 14/11/2026');
    }

    public function testRefusesInvalidSources(): void
    {
        $file = sys_get_temp_dir() . '/sources-essai-' . bin2hex(random_bytes(4)) . '.json';
        $data = json_decode((string) file_get_contents(Sources::FILE), true);
        self::assertIsArray($data);
        try {
            foreach (['registre' => 'http://registry.example.com/v0/servers', 'pages_max' => 0, 'par_page' => 'cent'] as $field => $value) {
                file_put_contents($file, (string) json_encode([$field => $value] + $data));
                try {
                    Sources::load($file);
                    self::fail($field . ' invalide accepté');
                } catch (SourceError $error) {
                    self::assertStringContainsString('« ' . $field . ' »', $error->getMessage());
                }
            }
        } finally {
            @unlink($file);
        }
    }

    public function testKeywordsMatchWholeWordsWithoutCaseOrAccents(): void
    {
        $keywords = Keywords::load();

        self::assertSame('france', $keywords->firstMatch(Text::simplify('Données sur la FRANCE.')));
        self::assertSame('francais', $keywords->firstMatch(Text::simplify('Un outil en Français')));
        self::assertTrue(Text::containsWord(Text::simplify('io.github.exemple/offres-france-travail'), Text::simplify('France Travail')), 'tiret ou espace : même mot-clé');
        self::assertSame('data.gouv', $keywords->firstMatch(Text::simplify('Jeux de données de data.gouv.fr')));
        self::assertSame('factur x', $keywords->firstMatch(Text::simplify('Lecture Factur-X')));
        self::assertNull($keywords->firstMatch(Text::simplify('Frances et Frankfurt, signature, ignition')), 'mots entiers seulement');
        self::assertTrue(Text::containsWord(Text::simplify('Agence IGN'), 'ign'));
        self::assertFalse(Text::containsWord(Text::simplify('design'), 'ign'));
    }

    public function testCategoriesUseTheFirstMatchingRule(): void
    {
        $categories = Categories::load();

        self::assertSame('autre', array_slice($categories->keys(), -1)[0] ?? null);
        self::assertSame('entreprises_registres', $categories->classify(self::server('Recherche SIREN et factures')), 'première règle gagnante');
        self::assertSame('fiscalite_facturation', $categories->classify(self::server('Factur-X pour la France')));
        self::assertSame('transport_mobilite', $categories->classify(self::server('Horaires SNCF')));
        self::assertSame('autre', $categories->classify(self::server('Météo en France')));
        self::assertSame('Santé', $categories->label('sante'));
    }

    public function testRefusesCategoriesWithoutOtherAtTheEnd(): void
    {
        $file = sys_get_temp_dir() . '/categories-essai-' . bin2hex(random_bytes(4)) . '.json';
        file_put_contents($file, (string) json_encode(['categories' => [
            ['cle' => 'autre', 'libelle' => 'Autre', 'mots_cles' => []],
            ['cle' => 'sante', 'libelle' => 'Santé', 'mots_cles' => ['sante']],
        ]]));
        try {
            $this->expectException(SourceError::class);
            $this->expectExceptionMessage('doit être la dernière');
            Categories::load($file);
        } finally {
            unlink($file);
        }
    }

    public function testCleansAndEscapesTexts(): void
    {
        self::assertSame('Deux espaces et une ligne', Text::clean("  Deux\u{00A0} espaces\net une   ligne "));
        self::assertNull(Text::clean(" \n "));
        self::assertSame('a\\|b \\*c\\* \\[lien\\](x) &lt;img&gt; \\`code\\`', Text::markdown('a|b *c* [lien](x) <img> `code`'));
    }

    private static function server(string $description): Server
    {
        return new Server('io.github.exemple/essai', null, $description, '1.0.0', null, null, [], [], null, null);
    }
}
