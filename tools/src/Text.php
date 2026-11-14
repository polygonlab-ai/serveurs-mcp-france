<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Textes : nettoyage des valeurs lues dans le registre, forme simplifiée pour la recherche de mots-clés,
 * échappement Markdown pour le tableau du README.
 */
final class Text
{
    /** Lettres accentuées (minuscules) et leur forme sans accent. */
    private const ACCENTS = [
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae',
        'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ñ' => 'n',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'œ' => 'oe',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ÿ' => 'y',
    ];

    /**
     * Texte nettoyé (caractères de contrôle, espaces insécables et retours à la ligne remplacés par une espace,
     * espaces réduits), ou null s'il est vide.
     */
    public static function clean(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        if (preg_match('//u', $value) !== 1) {
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }
        $value = trim((string) preg_replace('/[\x00-\x1F\x7F\x{00A0}\x{2007}\x{202F}\x{200B}\x{2028}\x{2029}\x{FEFF}\s]+/u', ' ', $value));

        return $value === '' ? null : $value;
    }

    /**
     * Forme simplifiée pour la recherche : minuscules sans accent ; tout caractère autre qu'une lettre, un chiffre
     * ou un point devient une espace (« France-Travail » donne « france travail », « data.gouv.fr » reste tel quel).
     */
    public static function simplify(string $value): string
    {
        $value = strtr(mb_strtolower($value, 'UTF-8'), self::ACCENTS);
        $value = (string) preg_replace('/[^a-z0-9.]+/', ' ', $value);

        return trim((string) preg_replace('/ {2,}/', ' ', $value));
    }

    /**
     * Le mot-clé (déjà simplifié) figure-t-il en mot entier dans le texte (déjà simplifié) ?
     */
    public static function containsWord(string $simplifiedText, string $simplifiedKeyword): bool
    {
        if ($simplifiedKeyword === '') {
            return false;
        }

        return preg_match('/(?<![a-z0-9])' . preg_quote($simplifiedKeyword, '/') . '(?![a-z0-9])/', $simplifiedText) === 1;
    }

    /**
     * Valeur affichée telle quelle dans une cellule de tableau Markdown : HTML neutralisé, caractères de mise en
     * forme et de lien échappés (aucun lien ni aucune image ne peut être fabriqué par une description).
     */
    public static function markdown(string $value): string
    {
        $value = htmlspecialchars($value, ENT_NOQUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');

        return (string) preg_replace('/([\\\\`*_\[\]|~])/', '\\\\$1', $value);
    }
}
