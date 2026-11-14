<?php

declare(strict_types=1);

// Liste publiée sur GitHub (ou copie locale passée en argument).
$adresse = $argv[1] ?? 'https://raw.githubusercontent.com/polygonlab-ai/serveurs-mcp-france/main/data/serveurs.json';
/** @var array{nombre: int, date_source: string, nombres: array<string, int>, serveurs: list<array{nom: string, titre: ?string, categorie: string, distant: list<array{type: string, url: string}>}>} $liste */
$liste = json_decode((string) file_get_contents($adresse), true, 16, JSON_THROW_ON_ERROR);

printf("%d serveurs MCP liés à la France, liste du %s\n", $liste['nombre'], $liste['date_source']);

// Les serveurs qui s'utilisent à distance (sans installation), avec leur adresse.
foreach ($liste['serveurs'] as $serveur) {
    foreach ($serveur['distant'] as $acces) {
        printf("%s (%s) : %s\n", $serveur['titre'] ?? $serveur['nom'], $serveur['categorie'], $acces['url']);
    }
}
