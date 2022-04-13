<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Option;
use TH\Maybe\Result;

/**
 * @template T
 * @template E
 * @implements Result<T, E>
 */
final class Err implements Result
{
    /**
     * @param E $value
     */
    public function __construct(private mixed $value) {}

    /**
     * @throws \RuntimeException
     */
    public function expect(string $message): never
    {
        if ($this->value instanceof \Throwable) {
            throw new \RuntimeException($message, previous: $this->value);
        }

        throw new \RuntimeException(\sprintf($message, \serialize($this->value)));
    }

    /**
     * @throws \Throwable
     */
    public function unwrap(): never
    {
        if ($this->value instanceof \Throwable) {
            throw $this->value;
        }

        $this->expect("Unwrapping `Err`: %s");
    }

    /**
     * @return E
     */
    public function unwrapErr(): mixed
    {
        return $this->value;
    }

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Or `$default` if the `Result` is `Err`.
     *
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    public function unwrapOrElse(callable $default): mixed
    {
        return $default($this->value);
    }

    /**
     * @return $this
     */
    public function inspect(callable $callback): self
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function inspectErr(callable $callback): self
    {
        $callback($this->value);

        return $this;
    }

    /**
     * @return $this
     */
    public function and(Result $right): self
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function andThen(callable $right): Result
    {
        return $this;
    }

    public function or(Result $right): Result
    {
        return $right;
    }

    public function orElse(callable $right): Result
    {
        return $right($this->value);
    }

    /**
     * @return false
     */
    public function contains(mixed $value, bool $strict = true): bool
    {
        return false;
    }

    public function containsErr(mixed $value, bool $strict = true): bool
    {
        return $strict
            ? ($this->value === $value)
            // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
            : ($this->value == $value);
    }

    /**
     * @return $this
     */
    public function map(callable $callback): Result
    {
        return $this;
    }

    /**
     * @template F
     * @param callable(E):F $callback
     * @return Result\Err<T, F>
     */
    public function mapErr(callable $callback): Result
    {
        /** @var Result\Err<T, F> */
        return Result\err($callback($this->value));
    }

    public function mapOr(callable $callback, mixed $default): mixed
    {
        return $default;
    }

    public function ok(): Option
    {
        /** @var Option<T> */
        return $this->mapOrElse(
            Option\some(...),
            Option\none(...),
        );
    }

    public function err(): Option
    {
        /** @var Option<E> */
        return $this->mapOrElse(
            Option\none(...),
            Option\some(...),
        );
    }

    public function mapOrElse(callable $callback, callable $default): mixed
    {
        return $default($this->value);
    }

    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
