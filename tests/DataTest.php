<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Tests;

use JsonSchema\Constraints\Constraint;
use JsonSchema\Validator;
use PHPUnit\Framework\TestCase;
use PolygonLab\ServeursMcpFrance\Outils\Categories;
use PolygonLab\ServeursMcpFrance\Outils\Command;
use PolygonLab\ServeursMcpFrance\Outils\DataFiles;
use PolygonLab\ServeursMcpFrance\Outils\JsonFile;
use PolygonLab\ServeursMcpFrance\Outils\Readme;
use PolygonLab\ServeursMcpFrance\Outils\Screening;
use PolygonLab\ServeursMcpFrance\Outils\Sources;
use PolygonLab\ServeursMcpFrance\Outils\Updater;
use PolygonLab\ServeursMcpFrance\Tests\Fixtures\Workspace;

/**
 * Les fichiers publiés de data/ : conformes au schéma, cohérents entre eux et avec le README, sans adresse e-mail.
 * Rejoué par l'intégration continue à chaque publication.
 */
final class DataTest extends TestCase
{
    private const DATA = __DIR__ . '/../data';

    private const SCHEMA = __DIR__ . '/../schema/serveurs.schema.json';

    public function testPublishedListMatchesTheSchema(): void
    {
        self::assertSchemaValid((string) file_get_contents(self::DATA . '/' . DataFiles::SERVERS_JSON));
    }

    public function testGeneratedListsMatchTheSchema(): void
    {
        $workspace = new Workspace();
        try {
            $process = proc_open([PHP_BINARY, __DIR__ . '/../tools/actualiser.php', '--pages', Workspace::PAGES, '--donnees', $workspace->data, '--readme', $workspace->readme, '--mots-interdits', $workspace->forbiddenWords], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
            self::assertIsResource($process);
            $output = (string) stream_get_contents($pipes[1]) . (string) stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            self::assertSame(Command::SUCCESS, proc_close($process), $output);
            $generated = $workspace->read(DataFiles::SERVERS_JSON);
        } finally {
            $workspace->remove();
        }

        self::assertSchemaValid($generated);
        self::assertSchemaValid(Updater::emptyFiles(Sources::load(), Categories::load())[DataFiles::SERVERS_JSON]);
    }

    public function testPublishedFilesAreConsistent(): void
    {
        $read = DataFiles::readServers((string) file_get_contents(self::DATA . '/' . DataFiles::SERVERS_JSON));
        $list = JsonFile::read(self::DATA . '/' . DataFiles::SERVERS_JSON, 'serveurs.json');
        $source = JsonFile::read(self::DATA . '/' . DataFiles::SOURCE_JSON, 'source.json');

        // Le CSV est le rendu exact du JSON, et le JSON est trié par nom.
        self::assertSame(DataFiles::serversCsv(array_values($read['servers']), $read['categories']), file_get_contents(self::DATA . '/' . DataFiles::SERVERS_CSV));
        $names = array_keys($read['servers']);
        $sorted = $names;
        sort($sorted, SORT_STRING);
        self::assertSame($sorted, $names);
        self::assertSame(\count($read['servers']), $list['nombre']);

        // Les compteurs par catégorie couvrent toutes les catégories et additionnent le total.
        $counts = $list['nombres'];
        self::assertIsArray($counts);
        self::assertSame(Categories::load()->keys(), array_keys($counts));
        self::assertSame($list['nombre'], array_sum($counts));

        // La source porte la même date ; une seule entrée, celle du registre, une fois la liste remplie.
        self::assertSame($list['date_source'], $source['date_source']);
        self::assertIsArray($source['fichiers']);
        if ($read['servers'] !== []) {
            self::assertSame(['registre'], array_column($source['fichiers'], 'cle'));
            self::assertSame(['https://registry.modelcontextprotocol.io/v0/servers?version=latest'], array_column($source['fichiers'], 'url'));
            foreach (array_column($source['fichiers'], 'sha256') as $hash) {
                self::assertIsString($hash);
                self::assertMatchesRegularExpression('/\A[0-9a-f]{64}\z/', $hash);
            }
        }

        // L'historique commence par son en-tête ; le tableau du README est celui des données publiées.
        self::assertStringStartsWith(DataFiles::historyHeader(), (string) file_get_contents(self::DATA . '/' . DataFiles::HISTORY_CSV));
        $readme = (string) file_get_contents(__DIR__ . '/../README.md');
        $date = $list['date_source'];
        $expected = Readme::block(\is_string($date) ? $date : null, array_values($read['servers']), $read['categories'], Categories::load());
        self::assertStringContainsString(Readme::START . $expected . Readme::END, $readme);
    }

    /**
     * Aucune adresse e-mail ne doit arriver dans data/ : celles des descriptions y sont remplacées.
     */
    public function testPublishedFilesContainNoEmailAddress(): void
    {
        foreach (glob(self::DATA . '/*') ?: [] as $file) {
            self::assertDoesNotMatchRegularExpression(Screening::EMAIL, (string) file_get_contents($file), basename($file) . ' contient une adresse e-mail');
            self::assertDoesNotMatchRegularExpression(Screening::IPV4, (string) file_get_contents($file), basename($file) . ' contient une adresse IPv4');
        }
    }

    private static function assertSchemaValid(string $json): void
    {
        $data = json_decode($json);
        $schema = json_decode((string) file_get_contents(self::SCHEMA));
        self::assertIsObject($schema, 'schéma illisible');

        $validator = new Validator();
        $validator->validate($data, $schema, Constraint::CHECK_MODE_NORMAL);

        self::assertTrue($validator->isValid(), (string) json_encode($validator->getErrors(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
