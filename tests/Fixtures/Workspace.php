<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Tests\Fixtures;

use PolygonLab\ServeursMcpFrance\Outils\Categories;
use PolygonLab\ServeursMcpFrance\Outils\Sources;
use PolygonLab\ServeursMcpFrance\Outils\Updater;

/**
 * Dossier de travail temporaire d'un test : dossier data/ vide mais valide, copie du README, fichier de mots
 * interdits d'essai, pages du registre d'essai, effacement.
 */
final class Workspace
{
    /** Mot interdit des essais (fictif). */
    public const FORBIDDEN_WORD = 'motinterdit';

    /** Pages du registre d'essai : deux pages, cas réels (versions multiples, champs absents, statut deprecated…). */
    public const PAGES = __DIR__ . '/registre';

    public readonly string $path;

    public readonly string $data;

    public readonly string $readme;

    public readonly string $forbiddenWords;

    public function __construct()
    {
        $this->path = sys_get_temp_dir() . '/serveurs-mcp-essai-' . bin2hex(random_bytes(6));
        $this->data = $this->path . '/data';
        mkdir($this->data, 0700, true);
        foreach (Updater::emptyFiles(Sources::load(), Categories::load()) as $name => $content) {
            file_put_contents($this->data . '/' . $name, $content);
        }
        $this->readme = $this->path . '/README.md';
        copy(__DIR__ . '/../../README.md', $this->readme);
        $this->forbiddenWords = $this->path . '/mots-interdits.json';
        file_put_contents($this->forbiddenWords, (string) json_encode(['mots' => [['mot' => self::FORBIDDEN_WORD, 'tolerees' => ['motinterdit-autorise']]]]));
    }

    /**
     * Copie des pages d'essai dans un dossier du test, pour les modifier.
     */
    public function pages(string $name = 'pages'): string
    {
        $directory = $this->path . '/' . $name;
        mkdir($directory, 0700, true);
        foreach (glob(self::PAGES . '/page-*.json') ?: [] as $file) {
            copy($file, $directory . '/' . basename($file));
        }

        return $directory;
    }

    public function read(string $name): string
    {
        return (string) file_get_contents($this->data . '/' . $name);
    }

    public function remove(): void
    {
        self::delete($this->path);
    }

    private static function delete(string $path): void
    {
        if (is_file($path) || is_link($path)) {
            unlink($path);

            return;
        }
        foreach (scandir($path) ?: [] as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                self::delete($path . '/' . $entry);
            }
        }
        rmdir($path);
    }
}
