<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Option>
 */
class Options implements IteratorAggregate
{
    /** @var array<string, Option> */
    protected array $options = [];

    /**
     * @param array<Option> $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
    }

    public function toArray(): array
    {
        return $this->options;
    }

    public function removeOption(string $name): void
    {
        unset($this->options[$name]);
    }

    /**
     * @return Options a new instance of Options with the merged options
     */
    public function merge(Options ...$optionsList): Options
    {
        $instance = clone $this;

        foreach ($optionsList as $options) {
            foreach ($options as $option) {
                $instance->addOption($option);
            }
        }

        return $instance;
    }

    public function addOption(Option $option): void
    {
        $this->options[$option->getIdentifier()] = $option;
    }

    public function __toString(): string
    {
        return implode(' ', $this->options);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->options);
    }
}
