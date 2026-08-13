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
        'align_multiline_comment' => true,

        // Spacing
        // 'no_blank_lines_after_class_opening' => false,
        'binary_operator_spaces' => true,  // $a= 1  + $b^ $d !==  $e or   $f; ==> $a = 1 + $b ^ $d !== $e or $f;
        'whitespace_after_comma_in_array' => true, // [1,2, 3,  4,    5] ==> [1, 2, 3, 4, 5]
        'no_singleline_whitespace_before_semicolons' => true, // $this->foo() ; ==> $this->foo();
        'object_operator_without_whitespace' => true, // $a  ->  b; ==> $a->b;
        'single_space_around_construct' => true, // $a  ->  b; ==> $a->b;
        'ternary_operator_spaces' => true, // return    true; ==> return true;
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
            ],
        ],
    ])
    ->setFinder($finder);
