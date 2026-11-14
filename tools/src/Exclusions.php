<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Liste d'exclusion (tools/exclusions.json) : serveurs retirés à la main de la liste publiée, par leur nom exact
 * dans le registre, chacun avec une raison courte et la date de la décision. Choix éditorial, pas un garde-fou.
 */
final class Exclusions
{
    public const FILE = __DIR__ . '/../exclusions.json';

    /**
     * @param array<string, array{raison: string, date: string}> $exclusions nom => raison et date
     */
    private function __construct(private readonly array $exclusions) {}

    /**
     * @throws SourceError
     */
    public static function load(string $path = self::FILE): self
    {
        $label = 'tools/exclusions.json';
        $data = JsonFile::read($path, $label);
        $list = $data['exclusions'] ?? null;
        if (!\is_array($list) || !array_is_list($list)) {
            throw new SourceError($label . ' : liste « exclusions » attendue (vide s\'il n\'y en a aucune).');
        }
        $exclusions = [];
        foreach ($list as $index => $entry) {
            $name = \is_array($entry) ? ($entry['nom'] ?? null) : null;
            $reason = \is_array($entry) ? ($entry['raison'] ?? null) : null;
            $date = \is_array($entry) ? ($entry['date'] ?? null) : null;
            if (!\is_string($name) || trim($name) === '' || !\is_string($reason) || trim($reason) === ''
                || !\is_string($date) || \DateTimeImmutable::createFromFormat('!Y-m-d', $date) === false) {
                throw new SourceError(sprintf('%s : exclusion n° %d : « nom », « raison » et « date » (AAAA-MM-JJ) attendus.', $label, $index + 1));
            }
            if (isset($exclusions[$name])) {
                throw new SourceError($label . ' : « ' . $name . ' » exclu deux fois.');
            }
            $exclusions[$name] = ['raison' => trim($reason), 'date' => $date];
        }

        return new self($exclusions);
    }

    /**
     * Raison de l'exclusion du serveur, ou null s'il n'est pas exclu.
     */
    public function reason(string $name): ?string
    {
        return $this->exclusions[$name]['raison'] ?? null;
    }
}
