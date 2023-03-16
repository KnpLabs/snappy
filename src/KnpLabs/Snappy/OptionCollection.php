<?php

declare(strict_types=1);

namespace KnpLabs\Snappy;

use ArrayIterator;
use IteratorAggregate;
use KnpLabs\Snappy\Option\SimpleOption;
use Traversable;

/**
 * @implements IteratorAggregate<int, string>
 */
class OptionCollection implements IteratorAggregate
{
    /** @var array<string, Option> */
    protected array $options = [];

    /**
     * @param array<Option> $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $option) {
            $this->add($option);
        }
    }

    /**
     * TODO: check this method
     *
     * @param iterable<int, string> $options
     */
    public static function fromFlatIterable(iterable $options): OptionCollection
    {
        $instance = new self();

        foreach ($options as $option) {
            $instance->add(new SimpleOption($option));
        }

        return $instance;
    }

    public function toArray(): array
    {
        return $this->options;
    }

    public function clear(): void
    {
        $this->options = [];
    }

    public function add(Option $option): void
    {
        $this->options[] = $option;
    }

    public function __toString(): string
    {
        return implode(' ', $this->options);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator(array_map(
            static fn (Option $option) => $option->__toString(),
            $this->options
        ));
    }
}
