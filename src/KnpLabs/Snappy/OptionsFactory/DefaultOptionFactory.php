<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\OptionsFactory;

use KnpLabs\Snappy\Option\FlagOption;
use KnpLabs\Snappy\Option\KeyValueOption;
use KnpLabs\Snappy\Option\SimpleOption;
use KnpLabs\Snappy\Options;
use KnpLabs\Snappy\OptionsFactory;

class DefaultOptionFactory implements OptionsFactory
{
    public function create(array $options): Options
    {
        $generatedOptions = new Options();

        foreach ($options as $name => $option) {
            if (null === $option || false === $option) {
                continue;
            }

            if (true === $option) {
                $generatedOptions->addOption(new FlagOption($name));
            } elseif (\is_array($option)) {
                if (self::isAssociativeArray($option)) {
                    foreach ($option as $key => $value) {
                        $generatedOptions->addOption(new KeyValueOption($name, $key, $value));
                    }
                } else {
                    foreach ($option as $value) {
                        $generatedOptions->addOption(new SimpleOption($name, $value));
                    }
                }
            } else {
                $generatedOptions->addOption(new SimpleOption($name, $option));
            }
        }

        return $generatedOptions;
    }

    protected static function isAssociativeArray(array $array): bool
    {
        return (bool) \count(\array_filter(\array_keys($array), 'is_string'));
    }
}
