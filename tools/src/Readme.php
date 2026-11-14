<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Tableau des serveurs par catégorie, généré dans le README entre deux balises de commentaire. Le reste du README
 * n'est jamais réécrit. Toute valeur venue du registre est échappée : aucun lien, aucune image ni aucun code HTML
 * ne peut être fabriqué par une description ; seules les adresses https du dépôt et du site deviennent des liens.
 */
final class Readme
{
    public const START = '<!-- liste-serveurs:debut -->';
    public const END = '<!-- liste-serveurs:fin -->';

    private const SAFE_URL = '~\Ahttps://[^\s<>()\[\]|"\'`\\\\]+\z~';

    /**
     * Contenu placé entre les balises.
     *
     * @param list<Server> $servers
     * @param array<string, string> $assigned nom du serveur => catégorie
     */
    public static function block(?string $sourceDate, array $servers, array $assigned, Categories $categories): string
    {
        if ($servers === [] || $sourceDate === null) {
            return "\n_La liste sera publiée ici après la première collecte du registre._\n\n";
        }
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $sourceDate);
        /** @var array<string, list<Server>> $byCategory */
        $byCategory = array_fill_keys($categories->keys(), []);
        foreach (DataFiles::sorted($servers) as $server) {
            $byCategory[$assigned[$server->name] ?? Categories::OTHER][] = $server;
        }
        $used = \count(array_filter($byCategory, static fn(array $list): bool => $list !== []));
        $lines = [
            '',
            sprintf(
                'Liste du %s : %s, %s. Fichier complet : [`data/serveurs.json`](data/serveurs.json).',
                $date === false ? $sourceDate : $date->format('d/m/Y'),
                Updater::plural(\count($servers), 'serveur', 'serveurs'),
                Updater::plural($used, 'catégorie', 'catégories'),
            ),
        ];
        foreach ($byCategory as $key => $list) {
            if ($list === []) {
                continue;
            }
            $lines[] = '';
            $lines[] = '### ' . $categories->label($key) . ' (' . \count($list) . ')';
            $lines[] = '';
            $lines[] = '| Serveur | Description | Version | Liens |';
            $lines[] = '|---|---|---|---|';
            foreach ($list as $server) {
                $lines[] = '| ' . self::name($server) . ' | ' . Text::markdown($server->description ?? '') . ' | ' . Text::markdown($server->version) . ' | ' . self::links($server) . ' |';
            }
        }

        return implode("\n", $lines) . "\n\n";
    }

    /**
     * README dont la partie entre les balises est remplacée.
     *
     * @throws SourceError
     */
    public static function replace(string $readme, string $block): string
    {
        $start = strpos($readme, self::START);
        $end = strpos($readme, self::END);
        if ($start === false || $end === false || $end < $start
            || substr_count($readme, self::START) !== 1 || substr_count($readme, self::END) !== 1) {
            throw new SourceError('README.md : balises « ' . self::START . ' » et « ' . self::END . ' » attendues, une fois chacune et dans cet ordre.');
        }

        return substr($readme, 0, $start + \strlen(self::START)) . $block . substr($readme, $end);
    }

    private static function name(Server $server): string
    {
        $code = str_contains($server->name, '`') ? Text::markdown($server->name) : '`' . $server->name . '`';

        return $server->title === null ? $code : '**' . Text::markdown($server->title) . '**<br>' . $code;
    }

    private static function links(Server $server): string
    {
        $links = [];
        foreach (['dépôt' => $server->repository, 'site' => $server->website] as $label => $url) {
            if ($url !== null && preg_match(self::SAFE_URL, $url) === 1 && !\in_array($url, $links, true)) {
                $links[$label] = $url;
            }
        }
        $parts = [];
        foreach ($links as $label => $url) {
            $parts[] = '[' . $label . '](' . $url . ')';
        }

        return implode(' · ', $parts);
    }
}
