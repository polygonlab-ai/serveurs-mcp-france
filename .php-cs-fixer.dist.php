<?php

declare(strict_types=1);

// Style : PER Coding Style, avec les règles « risquées » (déclaration stricte des types, classes finales).
$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__ . '/tools', __DIR__ . '/tests', __DIR__ . '/examples']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS' => true,
        '@PER-CS:risky' => true,
        'declare_strict_types' => true,
        'final_class' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'strict_comparison' => true,
        'strict_param' => true,
    ])
    ->setFinder($finder);
