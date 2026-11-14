<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Commande « php tools/actualiser.php » : arguments, collecte, lecture du manifeste, écriture des fichiers de
 * data/ et du tableau du README, résumé.
 *
 * Trois usages :
 *   - sans --collecter ni --manifeste : collecte puis actualisation, en une fois ;
 *   - --collecter <dossier> --manifeste <fichier.json> : collecte seule ; écrit le fichier normalisé dans le dossier
 *     et le manifeste (adresse, empreinte SHA-256, pages et entrées lues), sans toucher à data/ ;
 *   - --manifeste <fichier.json> : actualisation à partir d'une collecte déjà faite.
 *
 * Codes de sortie : 0 succès (fichiers réécrits, ou liste inchangée) ; 1 source refusée, rien n'est écrit ; 2 usage.
 *
 * @phpstan-import-type Collected from Updater
 */
final class Command
{
    public const SUCCESS = 0;
    public const REFUSED = 1;
    public const USAGE = 2;

    /** Nom du fichier normalisé écrit par la collecte. */
    public const COLLECTED_FILE = 'registre.json';

    public const MANIFEST_FORMAT = 1;

    /**
     * @param list<string> $arguments arguments de la ligne de commande, sans le nom du script
     * @param resource $output
     * @param resource $errors
     */
    public static function run(array $arguments, $output, $errors, ?\DateTimeImmutable $now = null, ?HttpClient $client = null): int
    {
        try {
            $options = self::options($arguments);
        } catch (\InvalidArgumentException $error) {
            fwrite($errors, $error->getMessage() . "\n\n" . self::usage());

            return self::USAGE;
        }
        if ($options['aide']) {
            fwrite($output, self::usage());

            return self::SUCCESS;
        }

        $temporary = null;
        try {
            $sources = Sources::load();
            $manifest = $options['manifeste'];
            $directory = $options['collecter'];
            if ($directory === null && $manifest !== null) {
                $collected = self::readManifest($manifest);
            } else {
                if ($directory === null) {
                    $temporary = sys_get_temp_dir() . '/serveurs-mcp-france-' . bin2hex(random_bytes(6));
                }
                $collected = self::collect($sources, $directory ?? (string) $temporary, $options, $client ?? self::client($sources, $options), $output);
                if ($directory !== null) {
                    self::writeManifest((string) $manifest, $collected);
                    fwrite($output, sprintf("Collecte : %d page(s), %d entrée(s) lue(s), %d serveur(s) actif(s), %d retenu(s) par les mots-clés.\nManifeste écrit : %s\n", $collected['pages'], $collected['read'], $collected['active'], \count($collected['servers']), (string) $manifest));

                    return self::SUCCESS;
                }
            }

            $screening = new Screening(
                Exclusions::load(),
                $options['mots-interdits'] === null ? ForbiddenWords::none() : ForbiddenWords::load($options['mots-interdits']),
            );
            $result = (new Updater($sources, Categories::load(), $screening, $options['donnees']))->run($collected, $now ?? new \DateTimeImmutable('now'));
            if ($result['changed']) {
                $readme = @file_get_contents($options['readme']);
                if ($readme === false) {
                    throw new SourceError('README illisible : ' . $options['readme'] . '.');
                }
                $files = [];
                foreach ($result['files'] as $name => $content) {
                    $files[$options['donnees'] . '/' . $name] = $content;
                }
                $files[$options['readme']] = Readme::replace($readme, $result['readme']);
                self::write($files);
            }
            if ($options['resume'] !== null && file_put_contents($options['resume'], JsonFile::encode($result['summary'])) === false) {
                throw new SourceError('Écriture du résumé impossible : ' . $options['resume'] . '.');
            }
            $text = $result['summary']['texte'];
            fwrite($output, (\is_string($text) ? $text : '') . "\n\n" . ($result['changed'] ? 'Fichiers de data/ et tableau du README réécrits.' : 'Liste inchangée : aucun fichier modifié.') . "\n");

            return self::SUCCESS;
        } catch (SourceError $error) {
            fwrite($errors, 'Arrêt : ' . $error->getMessage() . "\nAucun fichier n'a été écrit.\n");

            return self::REFUSED;
        } finally {
            if ($temporary !== null) {
                foreach (glob($temporary . '/*') ?: [] as $file) {
                    @unlink($file);
                }
                @rmdir($temporary);
            }
        }
    }

    public static function usage(): string
    {
        return <<<'TXT'
            Usage : php tools/actualiser.php [options]

            Sans option, parcourt tout le registre MCP officiel (tools/sources.json), retient les serveurs liés à la
            France, les compare à data/ et réécrit les fichiers et le tableau du README si la liste a changé.

              --collecter <dossier>         collecte seule : fichier normalisé écrit dans ce dossier (avec --manifeste)
              --manifeste <fichier.json>    avec --collecter : manifeste à écrire ; seul : collecte à utiliser
              --donnees <dossier>           dossier des données (défaut : data/ du dépôt)
              --readme <fichier.md>         README dont le tableau est réécrit (défaut : README.md du dépôt)
              --resume <fichier.json>       écrit le résumé des changements en JSON
              --mots-interdits <fichier>    mots qui écartent un serveur (JSON : {"mots": [{"mot": "…"}]})
              --pages <dossier>             rejoue une collecte enregistrée (page-0001.json…), sans réseau
              --enregistrer <dossier>       enregistre les pages lues, pour les rejouer avec --pages
              --chargeur <fichier.php>      chargeur automatique facultatif (défaut : vendor/autoload.php s'il existe)
              --aide                        affiche cette aide

            Codes de sortie : 0 succès, 1 source refusée (rien n'est écrit), 2 usage.

            TXT;
    }

    /**
     * Chargeur automatique désigné par --chargeur (lu avant toute autre option), sinon null.
     *
     * @param list<string> $arguments
     */
    public static function loader(array $arguments): ?string
    {
        foreach ($arguments as $index => $argument) {
            if ($argument === '--chargeur') {
                return $arguments[$index + 1] ?? null;
            }
            if (str_starts_with($argument, '--chargeur=')) {
                return substr($argument, \strlen('--chargeur='));
            }
        }

        return null;
    }

    /**
     * Empreinte des règles de sélection (mots-clés, catégories, exclusions) : une règle modifiée change l'empreinte
     * de la collecte, donc relance l'actualisation au passage suivant.
     *
     * @throws SourceError
     */
    public static function rulesHash(): string
    {
        $content = '';
        foreach ([Keywords::FILE, Categories::FILE, Exclusions::FILE] as $file) {
            $part = @file_get_contents($file);
            if ($part === false) {
                throw new SourceError(basename($file) . ' illisible.');
            }
            $content .= hash('sha256', $part);
        }

        return hash('sha256', $content);
    }

    /**
     * @param array{collecter: string|null, manifeste: string|null, donnees: string, readme: string, resume: string|null, mots-interdits: string|null, pages: string|null, enregistrer: string|null, aide: bool} $options
     */
    private static function client(Sources $sources, array $options): HttpClient
    {
        $client = $options['pages'] !== null ? new ReplayClient($options['pages']) : CurlClient::fromSources($sources);

        return $options['enregistrer'] !== null ? new RecordingClient($client, $options['enregistrer']) : $client;
    }

    /**
     * @param array{collecter: string|null, manifeste: string|null, donnees: string, readme: string, resume: string|null, mots-interdits: string|null, pages: string|null, enregistrer: string|null, aide: bool} $options
     * @param resource $output
     *
     * @return Collected
     *
     * @throws SourceError
     */
    private static function collect(Sources $sources, string $directory, array $options, HttpClient $client, $output): array
    {
        foreach (array_filter([$directory, $options['enregistrer']]) as $folder) {
            if (!is_dir($folder) && !@mkdir($folder, 0700, true)) {
                throw new SourceError('Dossier impossible à créer : ' . $folder . '.');
            }
        }
        $sleep = $options['pages'] !== null ? static function (int $seconds): void {} : null;
        fwrite($output, 'Collecte : ' . $sources->listUrl() . "\n");
        $result = (new Collector($client, $sources, Keywords::load(), $sleep))->collect();
        if ($result['servers'] === []) {
            throw new SourceError(sprintf('Liste vide : aucun serveur actif ne correspond aux mots-clés (%d entrée(s) lue(s) sur %d page(s)).', $result['read'], $result['pages']));
        }
        $content = Collector::normalizedFile($result['servers'], self::rulesHash());
        $path = rtrim($directory, '/') . '/' . self::COLLECTED_FILE;
        if (file_put_contents($path, $content) !== \strlen($content)) {
            throw new SourceError('Écriture impossible : ' . $path . '.');
        }

        return [
            'servers' => $result['servers'],
            'sha256' => hash('sha256', $content),
            'size' => \strlen($content),
            'pages' => $result['pages'],
            'read' => $result['read'],
            'active' => $result['active'],
            'url' => $sources->listUrl(),
            'path' => $path,
        ];
    }

    /**
     * @param Collected $collected
     *
     * @throws SourceError
     */
    private static function writeManifest(string $path, array $collected): void
    {
        $content = JsonFile::encode([
            'format' => self::MANIFEST_FORMAT,
            'mode' => 'collecte',
            'fichiers' => [[
                'cle' => Sources::KEY,
                'chemin' => $collected['path'],
                'url' => $collected['url'],
                'derniere_modification' => null,
                'sha256' => $collected['sha256'],
                'taille' => $collected['size'],
                'pages' => $collected['pages'],
                'entrees_lues' => $collected['read'],
                'serveurs_actifs' => $collected['active'],
            ]],
        ]);
        if (file_put_contents($path, $content) !== \strlen($content)) {
            throw new SourceError('Écriture du manifeste impossible : ' . $path . '.');
        }
    }

    /**
     * Collecte décrite par un manifeste : le fichier normalisé est relu et son empreinte vérifiée.
     *
     * @return Collected
     *
     * @throws SourceError
     */
    private static function readManifest(string $manifest): array
    {
        $data = JsonFile::read($manifest, 'Manifeste');
        $files = $data['fichiers'] ?? null;
        $entry = null;
        foreach (\is_array($files) ? $files : [] as $file) {
            if (\is_array($file) && ($file['cle'] ?? null) === Sources::KEY) {
                $entry = $file;
            }
        }
        $path = $entry['chemin'] ?? null;
        $url = $entry['url'] ?? null;
        $hash = $entry['sha256'] ?? null;
        if ($entry === null || !\is_string($path) || !\is_string($url) || !\is_string($hash)) {
            throw new SourceError('Manifeste : entrée « ' . Sources::KEY . ' » avec « chemin », « url » et « sha256 » attendue.');
        }
        $counts = [];
        foreach (['pages', 'entrees_lues', 'serveurs_actifs'] as $field) {
            $value = $entry[$field] ?? null;
            if (!\is_int($value) || $value < 0) {
                throw new SourceError('Manifeste : « ' . $field . ' » (entier) attendu.');
            }
            $counts[$field] = $value;
        }
        $content = @file_get_contents($path);
        if ($content === false) {
            throw new SourceError('Fichier de collecte illisible : ' . $path . '.');
        }
        if (!hash_equals($hash, hash('sha256', $content))) {
            throw new SourceError('Fichier de collecte : empreinte SHA-256 différente de celle du manifeste.');
        }

        return [
            'servers' => Collector::readNormalizedFile($content),
            'sha256' => $hash,
            'size' => \strlen($content),
            'pages' => $counts['pages'],
            'read' => $counts['entrees_lues'],
            'active' => $counts['serveurs_actifs'],
            'url' => $url,
            'path' => $path,
        ];
    }

    /**
     * @param list<string> $arguments
     *
     * @return array{collecter: string|null, manifeste: string|null, donnees: string, readme: string, resume: string|null, mots-interdits: string|null, pages: string|null, enregistrer: string|null, aide: bool}
     */
    private static function options(array $arguments): array
    {
        $root = \dirname(__DIR__, 2);
        $options = [
            'collecter' => null,
            'manifeste' => null,
            'donnees' => $root . '/data',
            'readme' => $root . '/README.md',
            'resume' => null,
            'mots-interdits' => null,
            'pages' => null,
            'enregistrer' => null,
            'aide' => false,
        ];
        $withValue = ['--collecter', '--manifeste', '--donnees', '--readme', '--resume', '--mots-interdits', '--pages', '--enregistrer', '--chargeur'];
        for ($i = 0, $count = \count($arguments); $i < $count; $i++) {
            $argument = $arguments[$i];
            $value = null;
            if (str_contains($argument, '=') && str_starts_with($argument, '--')) {
                [$argument, $value] = explode('=', $argument, 2);
            }
            if (\in_array($argument, $withValue, true) && $value === null) {
                $value = $arguments[++$i] ?? throw new \InvalidArgumentException('Valeur manquante après ' . $argument . '.');
            }
            if (\in_array($argument, $withValue, true) && $value === '') {
                throw new \InvalidArgumentException('Valeur vide après ' . $argument . '.');
            }
            switch ($argument) {
                case '--donnees':
                    $options['donnees'] = rtrim((string) $value, '/');
                    break;
                case '--collecter':
                    $options['collecter'] = (string) $value;
                    break;
                case '--manifeste':
                    $options['manifeste'] = (string) $value;
                    break;
                case '--readme':
                    $options['readme'] = (string) $value;
                    break;
                case '--resume':
                    $options['resume'] = (string) $value;
                    break;
                case '--mots-interdits':
                    $options['mots-interdits'] = (string) $value;
                    break;
                case '--pages':
                    $options['pages'] = (string) $value;
                    break;
                case '--enregistrer':
                    $options['enregistrer'] = (string) $value;
                    break;
                case '--chargeur':
                    break;
                case '--aide':
                case '--help':
                case '-h':
                    $options['aide'] = true;
                    break;
                default:
                    throw new \InvalidArgumentException('Argument non reconnu : ' . $argument . '.');
            }
        }
        if ($options['collecter'] !== null && $options['manifeste'] === null) {
            throw new \InvalidArgumentException('--collecter demande aussi --manifeste <fichier.json>.');
        }
        if (!$options['aide'] && $options['collecter'] === null && !is_dir($options['donnees'])) {
            throw new \InvalidArgumentException('Dossier des données introuvable : ' . $options['donnees'] . '.');
        }

        return $options;
    }

    /**
     * Écrit chaque fichier dans un fichier temporaire du même dossier, puis le renomme : un fichier n'est jamais
     * à moitié écrit.
     *
     * @param array<string, string> $files chemin => contenu
     *
     * @throws SourceError
     */
    private static function write(array $files): void
    {
        $temporaries = [];
        foreach ($files as $path => $content) {
            $temporary = \dirname($path) . '/.' . basename($path) . '.' . bin2hex(random_bytes(4)) . '.tmp';
            if (file_put_contents($temporary, $content) !== \strlen($content)) {
                foreach ($temporaries as $written) {
                    @unlink($written);
                }
                @unlink($temporary);

                throw new SourceError('Écriture impossible : ' . $path . '.');
            }
            @chmod($temporary, 0644);
            $temporaries[$path] = $temporary;
        }
        foreach ($temporaries as $path => $temporary) {
            if (!rename($temporary, $path)) {
                throw new SourceError('Remplacement impossible : ' . $path . '.');
            }
        }
    }
}
