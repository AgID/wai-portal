<?php
use PhpCsFixer\Finder;
use PhpCsFixer\Config;
use PhpCsFixer\FixerInterface;

$excluded_folders = [
    'config',
    'bootstrap/cache',
    'node_modules',
    'public',
    'resources',
    'storage',
    'vendor',
];

$rules = [
    '@PSR2' => true,
    '@Symfony' => true,
    'ordered_class_elements' => true,
    'array_syntax' => ['syntax' => 'short'],
    'concat_space' => ['spacing' => 'one'],
    'linebreak_after_opening_tag' => true,
    'no_empty_comment' => false,
    'no_superfluous_phpdoc_tags' => false,
    'ordered_imports' => true,
    'phpdoc_align' => ['align' => 'left'],
    'phpdoc_no_empty_return' => false,
    'phpdoc_order' => true,
    'global_namespace_import' => ['import_classes' => true],
    'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => true]
];

$finder = Finder::create()
    ->exclude($excluded_folders)
    ->notPath('server.php')
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
    ->in(__DIR__);

return (new Config())
    ->setRules($rules)
    ->setFinder($finder)
    ->setUsingCache(true);
