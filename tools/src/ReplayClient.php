<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Rejeu d'une collecte enregistrée (option --pages) : les pages page-0001.json, page-0002.json… du dossier sont
 * rendues dans l'ordre, une par demande, sans aucun appel réseau. Une collecte s'enregistre avec --enregistrer.
 */
final class ReplayClient implements HttpClient
{
    /** @var list<string> adresses demandées, dans l'ordre */
    public array $requests = [];

    public function __construct(private readonly string $directory) {}

    public static function file(string $directory, int $number): string
    {
        return rtrim($directory, '/') . sprintf('/page-%04d.json', $number);
    }

    public function get(string $url): string
    {
        $this->requests[] = $url;
        $file = self::file($this->directory, \count($this->requests));
        $body = is_file($file) ? @file_get_contents($file) : false;
        if ($body === false) {
            throw new SourceError('Page enregistrée absente : ' . basename($file) . ' (demandée pour ' . $url . ').');
        }

        return $body;
    }
}
