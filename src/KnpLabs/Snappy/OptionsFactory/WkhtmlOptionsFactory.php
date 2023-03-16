<?php

declare(strict_types=1);

namespace KnpLabs\Snappy\OptionsFactory;

use KnpLabs\Snappy\Option\ArraySimpleOption;
use KnpLabs\Snappy\Option\FlagOption;
use KnpLabs\Snappy\Option\FlagWithoutDashOption;
use KnpLabs\Snappy\Option\KeyValueOption;
use KnpLabs\Snappy\Option\SimpleOption;
use KnpLabs\Snappy\Option\SimpleWithoutDashOption;
use KnpLabs\Snappy\Options;
use KnpLabs\Snappy\OptionsFactory;

class WkhtmlOptionsFactory implements OptionsFactory
{
    public function create(array $options): Options
    {
        $generatedOptions = new Options();

        foreach ($options as $name => $option) {
            if (null === $option || false === $option) {
                continue;
            }

            if (true === $option) {
                $generatedOptions->addOption($name === 'toc'
                    ? new FlagWithoutDashOption($name)
                    : new FlagOption($name)
                );
            } elseif (\is_array($option)) {
                if (self::isAssociativeArray($option)) {
                    foreach ($option as $key => $value) {
                        $generatedOptions->addOption(new KeyValueOption($name, $key, $value));
                    }
                } else {
                    foreach ($option as $value) {
                        $generatedOptions->addOption(new ArraySimpleOption($name, $value, ' '));
                    }
                }
            } else {
                $option = in_array($name, ['toc', 'cover'])
                    ? new SimpleWithoutDashOption($name, $option)
                    : new SimpleOption($name, $option, ' ')
                ;

                $generatedOptions->addOption($option);
            }
        }

        return $generatedOptions;
    }

    protected static function isAssociativeArray(array $array): bool
    {
        return (bool) \count(\array_filter(\array_keys($array), 'is_string'));
    }
}
