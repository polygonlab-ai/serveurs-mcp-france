<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Source des données, déclarée dans tools/sources.json : adresse de l'API du registre MCP officiel et limites de la
 * collecte (taille des pages, plafond de pages, pause entre deux pages, essais, délais, taille).
 */
final class Sources
{
    public const FILE = __DIR__ . '/../sources.json';

    /** Clé de la source dans data/source.json et dans le manifeste de collecte. */
    public const KEY = 'registre';

    private function __construct(
        public readonly string $source,
        public readonly string $page,
        public readonly string $registry,
        public readonly int $perPage,
        public readonly int $maxPages,
        public readonly int $pauseSeconds,
        public readonly int $attempts,
        public readonly int $timeoutSeconds,
        public readonly int $maxPageSize,
        public readonly int $maxDurationSeconds,
    ) {}

    /**
     * @throws SourceError
     */
    public static function load(string $path = self::FILE): self
    {
        $data = JsonFile::read($path, 'tools/sources.json');
        $texts = [];
        foreach (['source', 'page', 'registre'] as $field) {
            $value = $data[$field] ?? null;
            if (!\is_string($value) || trim($value) === '') {
                throw new SourceError('tools/sources.json : champ « ' . $field . ' » (texte) attendu.');
            }
            $texts[$field] = $value;
        }
        foreach (['page', 'registre'] as $field) {
            if (preg_match('~\Ahttps://[^\s?#]+\z~', $texts[$field]) !== 1) {
                throw new SourceError('tools/sources.json : « ' . $field . ' » doit être une adresse https, sans paramètre.');
            }
        }

        return new self(
            $texts['source'],
            $texts['page'],
            $texts['registre'],
            self::integer($data, 'par_page', 1, 100),
            self::integer($data, 'pages_max', 1, 10000),
            self::integer($data, 'pause_s', 0, 60),
            self::integer($data, 'essais', 1, 10),
            self::integer($data, 'delai_s', 1, 600),
            self::integer($data, 'taille_max_page', 1000, 100_000_000),
            self::integer($data, 'duree_max_s', 10, 86400),
        );
    }

    /**
     * Adresse lue de la liste : dernière version de chaque serveur (« version=latest »).
     */
    public function listUrl(): string
    {
        return $this->registry . '?version=latest';
    }

    /**
     * Adresse d'une page de la liste.
     */
    public function pageUrl(?string $cursor): string
    {
        $url = $this->listUrl() . '&limit=' . $this->perPage;

        return $cursor === null ? $url : $url . '&cursor=' . rawurlencode($cursor);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws SourceError
     */
    private static function integer(array $data, string $field, int $min, int $max): int
    {
        $value = $data[$field] ?? null;
        if (!\is_int($value) || $value < $min || $value > $max) {
            throw new SourceError(sprintf('tools/sources.json : « %s » doit être un entier de %d à %d.', $field, $min, $max));
        }

        return $value;
    }
}
