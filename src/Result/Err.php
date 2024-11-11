<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Option;
use TH\Maybe\Result;

/**
 * @template E
 * @implements Result<never, E>
 * @nodoc
 */
class Err implements Result
{
    use MustBeUsed;

    /**
     * @param E $value
     * @nodoc
     */
    final public function __construct(private readonly mixed $value) {
        $this->mustBeUsed();
    }

    /**
     * @return false
     */
    final public function isOk(): bool
    {
        $this->used();

        return false;
    }

    /**
     * @return true
     */
    final public function isErr(): bool
    {
        $this->used();

        return true;
    }

    /**
     * @return false
     */
    final public function isOkAnd(callable $predicate): bool
    {
        $this->used();

        return false;
    }

    final public function isErrAnd(callable $predicate): bool
    {
        $this->used();

        return $predicate($this->value);
    }

    /**
     * @throws \RuntimeException
     */
    final public function expect(string $message): never
    {
        $this->used();

        if ($this->value instanceof \Throwable) {
            throw new \RuntimeException($message, previous: $this->value);
        }

        throw new \RuntimeException(\sprintf($message, \serialize($this->value)));
    }

    /**
     * @throws \Throwable
     */
    final public function unwrap(): never
    {
        $this->used();

        if ($this->value instanceof \Throwable) {
            throw $this->value;
        }

        throw new \RuntimeException(\sprintf("Unwrapping `Err`: %s", \serialize($this->value)));
    }

    /**
     * @return E
     * @phpstan-throws void
     */
    final public function unwrapErr(): mixed
    {
        $this->used();

        return $this->value;
    }

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Or `$default` if the `Result` is `Err`.
     *
     * @template U
     * @param U $default
     * @return U
     */
    final public function unwrapOr(mixed $default): mixed
    {
        $this->used();

        return $default;
    }

    /*
     * @template U
     * @param callable(E):U $default
     * @return U
     */
    final public function unwrapOrElse(callable $default): mixed
    {
        $this->used();

        return $default($this->value);
    }

    /**
     * @return $this
     */
    final public function inspect(callable $callback): self
    {
        return $this;
    }

    /**
     * @return $this
     */
    final public function inspectErr(callable $callback): self
    {
        $callback($this->value);

        return $this;
    }

    /**
     * @return self<E>
     */
    final public function and(Result $right): self
    {
        $this->used();
        $right->isOk(); // use this result

        return clone $this;
    }

    /**
     * @return self<E>
     */
    final public function andThen(callable $right): self
    {
        $this->used();

        return clone $this;
    }

    final public function or(Result $right): Result
    {
        $this->used();
        $right->ok(); // use this result

        return clone $right;
    }

    final public function orElse(callable $right): Result
    {
        $this->used();

        return $right($this->value);
    }

    /**
     * @return false
     */
    final public function contains(mixed $value, bool $strict = true): bool
    {
        $this->used();

        return false;
    }

    final public function containsErr(mixed $value, bool $strict = true): bool
    {
        $this->used();

        return $strict
            ? ($this->value === $value)
            // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
            : ($this->value == $value); // @phpstan-ignore equal.notAllowed
    }

    /**
     * @return self<E>
     */
    final public function map(callable $callback): self
    {
        $this->used();

        return clone $this;
    }

    /**
     * @template F
     * @param callable(E):F $callback
     * @return self<F>
     */
    final public function mapErr(callable $callback): self
    {
        $this->used();

        /** @var self<F> */
        return Result\err($callback($this->value));
    }

    final public function mapOr(callable $callback, mixed $default): mixed
    {
        $this->used();

        return $default;
    }

    final public function ok(): Option\None
    {
        $this->used();

        return Option\none();
    }

    /**
     * @return Option\Some<E>
     */
    final public function err(): Option\Some
    {
        $this->used();

        return Option\some($this->value);
    }

    final public function mapOrElse(callable $callback, callable $default): mixed
    {
        $this->used();

        return $default($this->value);
    }

    final public function getIterator(): \Traversable
    {
        $this->used();

        return new \EmptyIterator();
    }
}
