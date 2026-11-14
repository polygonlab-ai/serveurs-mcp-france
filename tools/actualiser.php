<?php

/**
 * Actualise la liste des serveurs MCP liés à la France à partir du registre MCP officiel.
 *
 * Usage :
 *   php tools/actualiser.php                         parcourt le registre, compare, réécrit data/ et le tableau du README
 *   php tools/actualiser.php --pages <dossier>       rejoue une collecte enregistrée (page-0001.json…), sans réseau
 *   php tools/actualiser.php --aide                  toutes les options
 *
 * Codes de sortie : 0 succès (ou liste inchangée), 1 source refusée (rien n'est écrit), 2 usage.
 * Aucune bibliothèque extérieure : extensions PHP curl, json et mbstring seulement ; vendor/ n'est pas nécessaire.
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit(1);
}

$arguments = array_values(array_filter(array_slice($argv, 1), 'is_string'));

// Chargeur automatique facultatif : celui désigné par --chargeur, sinon celui de Composer s'il est installé.
require_once __DIR__ . '/src/Command.php';
$loader = PolygonLab\ServeursMcpFrance\Outils\Command::loader($arguments);
if ($loader !== null && !is_file($loader)) {
    fwrite(STDERR, "Chargeur introuvable : {$loader}\n");
    exit(2);
}
$loader ??= __DIR__ . '/../vendor/autoload.php';
if (is_file($loader)) {
    require_once $loader;
}

// Classes de l'outil, chargées depuis tools/src/ dans tous les cas.
spl_autoload_register(static function (string $class): void {
    $prefix = 'PolygonLab\\ServeursMcpFrance\\Outils\\';
    if (str_starts_with($class, $prefix) && preg_match('/\A[A-Za-z0-9]+\z/', substr($class, strlen($prefix))) === 1) {
        $file = __DIR__ . '/src/' . substr($class, strlen($prefix)) . '.php';
        if (is_file($file)) {
            require $file;
        }
    }
});

exit(PolygonLab\ServeursMcpFrance\Outils\Command::run($arguments, STDOUT, STDERR));
