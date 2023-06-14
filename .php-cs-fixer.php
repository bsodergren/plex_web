<?php
/**
 * Command like Metatag writer for video files.
 */

$fileHeaderComment = <<<'EOF'
Command like Metatag writer for video files.
EOF;

return (new PhpCsFixer\Config())->setRules([
    //'@PhpCsFixer:risky'                                      => true,
    //'@PSR12:risky'                                     => true,
    //'@PER-CS1.0' => true,
    //'@PER-CS1.0:risky' => true,

    // '@PHP71Migration' => true,
    // '@PHPUnit75Migration:risky' => true,
     '@Symfony'                                        => true,
     '@Symfony:risky' => true,
    'protected_to_private'                             => false,
    'native_constant_invocation'                       => ['strict' => false],
    'nullable_type_declaration_for_default_null_value' => ['use_nullable_type_declaration' => false],
    'no_superfluous_phpdoc_tags'                       => ['remove_inheritdoc' => true],
    'phpdoc_add_missing_param_annotation'              => true,
    'header_comment'                                   => ['header' => $fileHeaderComment, 'comment_type' => 'PHPDoc', 'location' => 'after_open', 'separate' => 'bottom'],
    'modernize_strpos'                                 => true,
    'get_class_to_class_keyword'                       => true,
    'binary_operator_spaces'                           => [
        'operators' => [
            '=>'  => 'align_single_space_by_scope',
            '='   => 'align_single_space_by_scope',
//            '===' => 'align_single_space_minimal',
        ],
    ],
])
    ->setRiskyAllowed(true)
    ->setCacheFile('.php-cs-fixer.cache')
;
