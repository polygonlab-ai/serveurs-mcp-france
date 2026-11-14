<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Lecture d'une page de l'API du registre. Production : CurlClient. Rejeu d'une collecte enregistrée : ReplayClient.
 */
interface HttpClient
{
    /**
     * Corps de la réponse (réponse 200 exigée).
     *
     * @throws SourceError page impossible à lire
     */
    public function get(string $url): string;
}
