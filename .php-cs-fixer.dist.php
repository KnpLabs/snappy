<?php

declare(strict_types=1);
use PedroTroller\CS\Fixer\Fixers;
use PedroTroller\CS\Fixer\RuleSetFactory;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/bin')
    ->append([__FILE__])
;

$rules = RuleSetFactory::create()
    ->phpCsFixer(true)
    ->php(8.1, true)
    ->phpunit(11.0, true)
    ->pedrotroller(true)
    ->disable('PedroTroller/line_break_between_method_arguments')
    ->getRules()
;

return (new Config())
    ->setRiskyAllowed(true)
    ->registerCustomFixers(new Fixers())
    ->setRules($rules)
    ->setFinder($finder)
;
