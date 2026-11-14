<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Mots-clés qui rattachent un serveur à la France (tools/mots-cles.json), cherchés sans tenir compte des
 * majuscules ni des accents, en mots entiers, dans le nom, le titre et la description.
 */
final class Keywords
{
    public const FILE = __DIR__ . '/../mots-cles.json';

    /**
     * @param list<string> $simplified mots-clés simplifiés (Text::simplify), sans doublon
     */
    private function __construct(public readonly array $simplified) {}

    /**
     * @throws SourceError
     */
    public static function load(string $path = self::FILE): self
    {
        $data = JsonFile::read($path, 'tools/mots-cles.json');
        $keywords = JsonFile::strings($data['mots_cles'] ?? null, 'tools/mots-cles.json, « mots_cles »');
        if ($keywords === []) {
            throw new SourceError('tools/mots-cles.json : au moins un mot-clé attendu.');
        }

        return self::fromList($keywords, 'tools/mots-cles.json');
    }

    /**
     * @param list<string> $keywords
     *
     * @throws SourceError
     */
    public static function fromList(array $keywords, string $label): self
    {
        $simplified = [];
        foreach ($keywords as $keyword) {
            $form = Text::simplify($keyword);
            if ($form === '') {
                throw new SourceError($label . ' : mot-clé « ' . $keyword . ' » vide une fois simplifié.');
            }
            if (!\in_array($form, $simplified, true)) {
                $simplified[] = $form;
            }
        }

        return new self($simplified);
    }

    /**
     * Premier mot-clé trouvé dans le texte (simplifié), ou null.
     */
    public function firstMatch(string $simplifiedText): ?string
    {
        foreach ($this->simplified as $keyword) {
            if (Text::containsWord($simplifiedText, $keyword)) {
                return $keyword;
            }
        }

        return null;
    }
}
