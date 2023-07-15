<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Option;
use TH\Maybe\Result;

/**
 * @template T
 * @template E
 * @implements Result<T, E>
 * @nodoc
 */
class Ok implements Result
{
    use MustBeUsed;

    /**
     * @param T $value
     * @nodoc
     */
    final public function __construct(private readonly mixed $value) {
        $this->mustBeUsed();
    }

    /**
     * @return true
     */
    final public function isOk(): bool
    {
        $this->used();

        return true;
    }

    /**
     * @return false
     */
    final public function isErr(): bool
    {
        $this->used();

        return false;
    }

    final public function isOkAnd(callable $predicate): bool
    {
        $this->used();

        return $predicate($this->value);
    }

    /**
     * @return false
     */
    final public function isErrAnd(callable $predicate): bool
    {
        $this->used();

        return false;
    }

    /**
     * @return T
     * @phpstan-throws void
     */
    final public function expect(string $message): mixed
    {
        $this->used();

        return $this->value;
    }

    /**
     * @return T
     * @phpstan-throws void
     */
    final public function unwrap(): mixed
    {
        $this->used();

        return $this->value;
    }

    /**
     * @throws \RuntimeException
     */
    final public function unwrapErr(): never
    {
        $this->used();

        throw new \RuntimeException(\sprintf("Unwrapping err on `Ok`: %s", \serialize($this->value)));
    }

    final public function unwrapOr(mixed $default): mixed
    {
        $this->used();

        return $this->value;
    }

    final public function unwrapOrElse(callable $default): mixed
    {
        $this->used();

        return $this->value;
    }

    /**
     * @return $this
     */
    final public function inspect(callable $callback): self
    {
        $callback($this->value);

        return $this;
    }

    /**
     * @return $this
     */
    final public function inspectErr(callable $callback): self
    {
        return $this;
    }

    final public function and(Result $right): Result
    {
        $this->used();
        $right->ok(); // use this result

        return clone $right;
    }

    /**
     * @template U
     * @template F
     * @param callable(T):Result<U, F> $right
     * @return Result<U, F>
     */
    final public function andThen(callable $right): Result
    {
        $this->used();

        return $right($this->value);
    }

    /**
     * @return self<T,E>
     */
    final public function or(Result $right): self
    {
        $this->used();
        $right->ok(); // use this result

        return clone $this;
    }

    /**
     * @return self<T,E>
     */
    final public function orElse(callable $right): self
    {
        $this->used();

        return clone $this;
    }

    final public function contains(mixed $value, bool $strict = true): bool
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
    final public function containsErr(mixed $value, bool $strict = true): bool
    {
        $this->used();

        return false;
    }

    /**
     * @template U
     * @param callable(T):U $callback
     * @return Result<U, E>
     */
    final public function map(callable $callback): self
    {
        $this->used();

        /** @var Result\Ok<U, E> */
        return Result\ok($callback($this->value));
    }

    /**
     * @return self<T,E>
     */
    final public function mapErr(callable $callback): self
    {
        $this->used();

        return clone $this;
    }

    final public function mapOr(callable $callback, mixed $default): mixed
    {
        $this->used();

        return $callback($this->value);
    }

    final public function mapOrElse(callable $callback, callable $default): mixed
    {
        $this->used();

        return $callback($this->value);
    }

    /**
     * @return Option\Some<T>
     */
    final public function ok(): Option\Some
    {
        $this->used();

        /** @var Option\Some<T> */
        return Option\some($this->value);
    }

    /**
     * @return Option\None<E>
     */
    final public function err(): Option\None
    {
        $this->used();

        /** @var Option\None<E> */
        return Option\none();
    }

    final public function getIterator(): \Traversable
    {
        $this->used();

        yield $this->value;
    }
}
