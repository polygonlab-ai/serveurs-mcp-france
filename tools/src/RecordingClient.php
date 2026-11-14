<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Enregistrement d'une collecte (option --enregistrer) : chaque page lue est copiée telle quelle dans le dossier
 * (page-0001.json, page-0002.json…), pour être rejouée plus tard avec --pages.
 */
final class RecordingClient implements HttpClient
{
    private int $count = 0;

    public function __construct(
        private readonly HttpClient $client,
        private readonly string $directory,
    ) {}

    public function get(string $url): string
    {
        $body = $this->client->get($url);
        $this->count++;
        $file = ReplayClient::file($this->directory, $this->count);
        if (file_put_contents($file, $body) !== \strlen($body)) {
            throw new SourceError('Enregistrement impossible : ' . $file . '.');
        }

        return $body;
    }
}
