<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Catégories des serveurs (tools/categories.json) : la première catégorie dont un mot-clé figure dans le nom, le
 * titre ou la description du serveur l'emporte ; sinon « autre », dernière catégorie, sans mot-clé.
 */
final class Categories
{
    public const FILE = __DIR__ . '/../categories.json';

    public const OTHER = 'autre';

    /**
     * @param list<array{cle: string, libelle: string, mots_cles: Keywords|null}> $categories
     */
    private function __construct(private readonly array $categories) {}

    /**
     * @throws SourceError
     */
    public static function load(string $path = self::FILE): self
    {
        $label = 'tools/categories.json';
        $data = JsonFile::read($path, $label);
        $list = $data['categories'] ?? null;
        if (!\is_array($list) || !array_is_list($list) || $list === []) {
            throw new SourceError($label . ' : liste « categories » attendue.');
        }
        $categories = [];
        $keys = [];
        foreach ($list as $index => $entry) {
            $key = \is_array($entry) ? ($entry['cle'] ?? null) : null;
            $name = \is_array($entry) ? ($entry['libelle'] ?? null) : null;
            if (!\is_string($key) || preg_match('/\A[a-z][a-z_]*\z/', $key) !== 1 || !\is_string($name) || trim($name) === '' || \in_array($key, $keys, true)) {
                throw new SourceError(sprintf('%s : catégorie n° %d sans clé unique (minuscules et « _ ») ou sans libellé.', $label, $index + 1));
            }
            $keywords = JsonFile::strings($entry['mots_cles'] ?? null, $label . ', catégorie « ' . $key . ' »');
            $last = $index === \count($list) - 1;
            if ($last !== ($key === self::OTHER) || ($key === self::OTHER) !== ($keywords === [])) {
                throw new SourceError($label . ' : la catégorie « ' . self::OTHER . ' » doit être la dernière, la seule sans mot-clé.');
            }
            $keys[] = $key;
            $categories[] = ['cle' => $key, 'libelle' => $name, 'mots_cles' => $keywords === [] ? null : Keywords::fromList($keywords, $label)];
        }

        return new self($categories);
    }

    /**
     * Catégorie d'un serveur.
     */
    public function classify(Server $server): string
    {
        $text = $server->searchText();
        foreach ($this->categories as $category) {
            if ($category['mots_cles'] !== null && $category['mots_cles']->firstMatch($text) !== null) {
                return $category['cle'];
            }
        }

        return self::OTHER;
    }

    /**
     * Clés des catégories, dans l'ordre du fichier.
     *
     * @return list<string>
     */
    public function keys(): array
    {
        return array_column($this->categories, 'cle');
    }

    public function label(string $key): string
    {
        foreach ($this->categories as $category) {
            if ($category['cle'] === $key) {
                return $category['libelle'];
            }
        }

        return $key;
    }
}
