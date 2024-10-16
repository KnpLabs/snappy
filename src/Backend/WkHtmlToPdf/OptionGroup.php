<?php

declare(strict_types=1);

namespace KNPLabs\Snappy\Backend\WkHtmlToPdf;

abstract class OptionGroup {
    /** @return array<string|int|float> */
    public function compile(): array
    {
        $options = [];

        foreach ($this as $property) {
            if ($property instanceof Option) {
                $options = array_merge($options, $property->compile());
            }
        }

        return $options;
    }
}
