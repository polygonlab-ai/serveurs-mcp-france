<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Lecture et écriture JSON : objets lus avec un message clair en cas d'erreur, rendu déterministe (mêmes données,
 * mêmes octets).
 */
final class JsonFile
{
    private const FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR;

    /**
     * Objet JSON lu dans un fichier.
     *
     * @return array<string, mixed>
     *
     * @throws SourceError
     */
    public static function read(string $path, string $label): array
    {
        $content = @file_get_contents($path);
        if ($content === false) {
            throw new SourceError($label . ' illisible.');
        }

        return self::decode($content, $label);
    }

    /**
     * Objet JSON décodé.
     *
     * @return array<string, mixed>
     *
     * @throws SourceError
     */
    public static function decode(string $content, string $label): array
    {
        try {
            $data = json_decode($content, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $error) {
            throw new SourceError($label . ' : JSON invalide (' . $error->getMessage() . ').');
        }
        if (!\is_array($data) || ($data !== [] && array_is_list($data))) {
            throw new SourceError($label . ' : objet JSON attendu.');
        }

        /** @var array<string, mixed> $data */
        return $data;
    }

    /**
     * @param array<mixed> $data
     */
    public static function encode(array $data): string
    {
        return json_encode($data, self::FLAGS) . "\n";
    }

    /**
     * Liste de textes non vides, sans doublon.
     *
     * @return list<string>
     *
     * @throws SourceError
     */
    public static function strings(mixed $value, string $label): array
    {
        if (!\is_array($value) || !array_is_list($value)) {
            throw new SourceError($label . ' : liste de textes attendue.');
        }
        $strings = [];
        foreach ($value as $item) {
            if (!\is_string($item) || trim($item) === '') {
                throw new SourceError($label . ' : chaque élément doit être un texte non vide.');
            }
            $strings[] = $item;
        }
        if (\count(array_unique($strings)) !== \count($strings)) {
            throw new SourceError($label . ' : élément en double.');
        }

        return $strings;
    }
}
