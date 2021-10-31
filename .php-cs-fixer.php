<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->registerCustomFixers(new PedroTroller\CS\Fixer\Fixers())
    ->setRules([
        '@PHP71Migration' => true,
        '@PSR1' => true,
        '@PSR2' => true,
        'array_indentation' => true,
        'array_syntax' => [ 'syntax' => 'short' ],
        'align_multiline_comment' => [
            'comment_type' => 'all_multiline',
        ],
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'binary_operator_spaces' => true,
        'blank_line_after_opening_tag' => true,
        'blank_line_before_statement' => true,
        'cast_spaces' => true,
        'class_attributes_separation' => true,
        'combine_consecutive_issets' => true,
        'combine_consecutive_unsets' => true,
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'date_time_immutable' => true,
        'declare_equal_normalize' => [
            'space' => 'single',
        ],
        'dir_constant' => true,
        'ereg_to_preg' => true,
        'escape_implicit_backslashes' => true,
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'fopen_flag_order' => true,
        'fopen_flags' => true,
        'fully_qualified_strict_types' => true,
        'function_to_constant' => [
            'functions' => [
                'get_class',
                'php_sapi_name',
                'phpversion',
                'pi',
            ]
        ],
        'function_typehint_space' => true,
        'global_namespace_import' => true,
        'heredoc_to_nowdoc' => true,
        'implode_call' => true,
        'include' => true,
        'is_null' => true,
        'linebreak_after_opening_tag' => true,
        'list_syntax' => [
            'syntax' => 'long',
        ],
        'logical_operators' => true,
        'lowercase_cast' => true,
        'lowercase_static_reference' => true,
        'magic_constant_casing' => true,
        'magic_method_casing' => true,
        'method_chaining_indentation' => true,
        'modernize_types_casting' => true,
        'multiline_comment_opening_closing' => true,
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'new_line_for_chained_calls',
        ],
        'native_constant_invocation' => [
            'include' => ['@internal'],
            'scope' => 'all',
        ],
        'native_function_casing' => true,
        'native_function_invocation' => ['include' => ['@all']],
        'native_function_type_declaration_casing' => true,
        'new_with_braces' => true,
        'no_alternative_syntax' => true,
        'no_binary_string' => true,
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,
        'no_extra_blank_lines' => [
            'tokens' => ['extra']
        ],
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_mixed_echo_print' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_short_bool_cast' => true,
        'echo_tag_syntax' => ['format' => 'long'],
        'no_singleline_whitespace_before_semicolons' => true,
        'no_spaces_around_offset' => [
            'positions' => ['inside', 'outside'],
        ],
        'no_superfluous_elseif' => true,
        'no_superfluous_phpdoc_tags' => false,
        'no_trailing_comma_in_list_call' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unneeded_curly_braces' => true,
        'no_unused_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'normalize_index_brace' => true,
        'object_operator_without_whitespace' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public',
                'method_protected',
                'method_private'
            ],
        ],
        'php_unit_construct' => [
            'assertions' => [
                'assertEquals',
                'assertSame',
                'assertNotEquals',
                'assertNotSame'
            ]
        ],
        // Check on other phpunit stuff
        'phpdoc_align' => [
            'align' => 'vertical'
        ],
        'phpdoc_indent' => true,
        'general_phpdoc_tag_rename' => ['fix_inline' => true],
        'phpdoc_no_access' => true,
        'phpdoc_no_alias_tag' => true,
        'phpdoc_no_empty_return' => false,
        'phpdoc_no_package' => true,
        'phpdoc_no_useless_inheritdoc' => true,
        'phpdoc_order' => true,
        'phpdoc_return_self_reference' => true,
        'phpdoc_scalar' => true,
        'phpdoc_separation' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_summary' => true,
        'phpdoc_to_comment' => true,
        'phpdoc_trim' => true,
        'phpdoc_trim_consecutive_blank_line_separation' => true,
        'phpdoc_types' => true,
        'phpdoc_types_order' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_var_without_name' => true,
        'psr_autoloading' => ['dir' => './src'],
        'return_assignment' => true,
        'return_type_declaration' => true,
        'semicolon_after_instruction' => true,
        'short_scalar_cast' => true,
        'single_blank_line_before_namespace' => true,
        'single_line_comment_style' => true,
        'single_line_throw' => true,
        'single_quote' => true,
        'single_trait_insert_per_statement' => true,
        'space_after_semicolon' => true,
        'standardize_increment' => true,
        'standardize_not_equals' => true,
        'strict_comparison' => true,
        'ternary_operator_spaces' => true,
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'trim_array_spaces' => true,
        'unary_operator_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'PedroTroller/exceptions_punctuation' => true,
        'PedroTroller/line_break_between_method_arguments' => [
            'max-args' => 5,
        ],
        'PedroTroller/useless_code_after_return' => true,
    ])
;
