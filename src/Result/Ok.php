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
    use MustBeUsed;

    /** @param T $value */
    public function __construct(private mixed $value) {
        $this->mustBeUsed();
    }

    /**
     * @return T
     */
    public function expect(string $message): mixed
    {
        $this->used();

        return $this->value;
    }

    /**
     * @return T
     */
    public function unwrap(): mixed
    {
        $this->used();

        return $this->value;
    }

    /**
     * @throws \RuntimeException
     */
    public function unwrapErr(): never
    {
        $this->used();

        throw new \RuntimeException(\sprintf("Unwrapping err on `Ok`: %s", \serialize($this->value)));
    }

    public function unwrapOr(mixed $default): mixed
    {
        $this->used();

        return $this->value;
    }

    public function unwrapOrElse(callable $default): mixed
    {
        $this->used();

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
        $this->used();
        $right->ok(); // use this result

        return clone $right;
    }

    public function andThen(callable $right): Result
    {
        $this->used();

        return $right($this->value);
    }

    /**
     * @return self<T,E>
     */
    public function or(Result $right): Result
    {
        $this->used();
        $right->ok(); // use this result

        return clone $this;
    }

    /**
     * @return self<T,E>
     */
    public function orElse(callable $right): Result
    {
        $this->used();

        return clone $this;
    }

    public function contains(mixed $value, bool $strict = true): bool
    {
        $this->used();

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
        $this->used();

        return false;
    }

    /**
     * @template U
     * @param callable(T):U $callback
     * @return Result<U, E>
     */
    public function map(callable $callback): Result
    {
        $this->used();

        /** @var Result\Ok<U, E> */
        return Result\ok($callback($this->value));
    }

    /**
     * @return self<T,E>
     */
    public function mapErr(callable $callback): Result
    {
        $this->used();

        return clone $this;
    }

    public function mapOr(callable $callback, mixed $default): mixed
    {
        $this->used();

        return $callback($this->value);
    }

    public function mapOrElse(callable $callback, callable $default): mixed
    {
        $this->used();

        return $callback($this->value);
    }

    /**
     * @return Option\Some<T>
     */
    public function ok(): Option
    {
        $this->used();

        /** @var Option\Some<T> */
        return Option\some($this->value);
    }

    /**
     * @return Option\None<E>
     */
    public function err(): Option
    {
        $this->used();

        /** @var Option\None<E> */
        return Option\none();
    }

    public function getIterator(): \Traversable
    {
        $this->used();

        yield $this->value;
    }
}
