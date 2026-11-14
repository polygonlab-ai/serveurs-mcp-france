<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Collecte ou actualisation refusée : registre injoignable, réponse illisible, entrée sans champ obligatoire,
 * plafond de pages atteint, liste vide, configuration invalide.
 *
 * Le message dit ce qui ne va pas et où (page, entrée, fichier), pour qu'on sache quoi vérifier sans lire le code.
 * Quand cette exception est levée, rien n'est écrit dans data/ ni dans le README.
 */
final class SourceError extends \RuntimeException {}
