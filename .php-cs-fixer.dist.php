<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,

        // Imports
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
        ],
        'no_unused_imports' => true,
        'no_unneeded_import_alias' => true,

        // Spacing
        // 'no_blank_lines_after_class_opening' => false,
        'whitespace_after_comma_in_array' => true,
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
            ],
        ],
    ])
    ->setFinder($finder);
