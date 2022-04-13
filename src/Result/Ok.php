<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Option;
use TH\Maybe\Result;

/**
 * @template T
 * @template E
 * @implements Result<T, E>
 */
final class Ok implements Result
{
    /** @param T $value */
    public function __construct(private mixed $value) {}

    /**
     * @return T
     */
    public function expect(string $message): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        return $this->value;
    }

    /**
     * @throws \RuntimeException
     */
    public function unwrapErr(): never
    {
        throw new \RuntimeException(\sprintf("Unwrapping err on `Ok`: %s", \serialize($this->value)));
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    public function unwrapOrElse(callable $default): mixed
    {
        return $this->value;
    }

    /**
     * @return $this
     */
    public function inspect(callable $callback): Result
    {
        $callback($this->value);

        return $this;
    }

    /**
     * @return $this
     */
    public function inspectErr(callable $callback): Result
    {
        return $this;
    }

    public function and(Result $right): Result
    {
        return $right;
    }

    public function andThen(callable $right): Result
    {
        return $right($this->value);
    }

    /**
     * @return $this
     */
    public function or(Result $right): Result
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function orElse(callable $right): Result
    {
        return $this;
    }

    public function contains(mixed $value, bool $strict = true): bool
    {
        return $strict
            ? ($this->value === $value)
            // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
            : ($this->value == $value);
    }

    /**
     * @return false
     */
    public function containsErr(mixed $value, bool $strict = true): bool
    {
        return false;
    }

    /**
     * @template U
     * @param callable(T):U $callback
     * @return Result<U, E>
     */
    public function map(callable $callback): Result
    {
        /** @var Result\Ok<U, E> */
        return Result\ok($callback($this->value));
    }

    /**
     * @return $this
     */
    public function mapErr(callable $callback): Result
    {
        return $this;
    }

    public function mapOr(callable $callback, mixed $default): mixed
    {
        return $callback($this->value);
    }

    public function mapOrElse(callable $callback, callable $default): mixed
    {
        return $callback($this->value);
    }

    /**
     * @return Option\Some<T>
     */
    public function ok(): Option
    {
        /** @var Option\Some<T> */
        return Option\some($this->value);
    }

    /**
     * @return Option\None<E>
     */
    public function err(): Option
    {
        /** @var Option\None<E> */
        return Option\none();
    }

    public function getIterator(): \Traversable
    {
        yield $this->value;
    }
}
