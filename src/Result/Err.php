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
final class Err implements Result
{
    use MustBeUsed;

    /**
     * @param E $value
     * @nodoc
     */
    public function __construct(private readonly mixed $value) {
        $this->mustBeUsed();
    }

    /**
     * @return false
     */
    public function isOk(): bool
    {
        $this->used();

        return false;
    }

    /**
     * @return true
     */
    public function isErr(): bool
    {
        $this->used();

        return true;
    }

    /**
     * @return false
     */
    public function isOkAnd(callable $predicate): bool
    {
        $this->used();

        return false;
    }

    public function isErrAnd(callable $predicate): bool
    {
        $this->used();

        return $predicate($this->value);
    }

    /**
     * @throws \RuntimeException
     */
    public function expect(string $message): never
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
    public function unwrap(): never
    {
        $this->used();

        if ($this->value instanceof \Throwable) {
            throw $this->value;
        }

        $this->expect("Unwrapping `Err`: %s");
    }

    /**
     * @return E
     * @phpstan-throws void
     */
    public function unwrapErr(): mixed
    {
        $this->used();

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
        $this->used();

        return $default;
    }

    public function unwrapOrElse(callable $default): mixed
    {
        $this->used();

        return $default($this->value);
    }

    /**
     * @return self<T,E>
     */
    public function inspect(callable $callback): self
    {
        $this->used();

        return clone $this;
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
     * @return self<T,E>
     */
    public function and(Result $right): self
    {
        $this->used();
        $right->ok(); // use this result

        return clone $this;
    }

    /**
     * @return self<T,E>
     */
    public function andThen(callable $right): Result
    {
        $this->used();

        return clone $this;
    }

    public function or(Result $right): Result
    {
        $this->used();
        $right->ok(); // use this result

        return clone $right;
    }

    public function orElse(callable $right): Result
    {
        $this->used();

        return $right($this->value);
    }

    /**
     * @return false
     */
    public function contains(mixed $value, bool $strict = true): bool
    {
        $this->used();

        return false;
    }

    public function containsErr(mixed $value, bool $strict = true): bool
    {
        $this->used();

        return $strict
            ? ($this->value === $value)
            // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
            : ($this->value == $value);
    }

    /**
     * @return self<T,E>
     */
    public function map(callable $callback): Result
    {
        $this->used();

        return clone $this;
    }

    /**
     * @template F
     * @param callable(E):F $callback
     * @return Result\Err<T, F>
     */
    public function mapErr(callable $callback): Result
    {
        $this->used();

        /** @var Result\Err<T, F> */
        return Result\err($callback($this->value));
    }

    public function mapOr(callable $callback, mixed $default): mixed
    {
        $this->used();

        return $default;
    }

    /**
     * @return Option\None<E>
     */
    public function ok(): Option
    {
        $this->used();

        /** @var Option\None<E> */
        return Option\none();
    }

    /**
     * @return Option\Some<E>
     */
    public function err(): Option
    {
        $this->used();

        return Option\some($this->value);
    }

    public function mapOrElse(callable $callback, callable $default): mixed
    {
        $this->used();

        return $default($this->value);
    }

    public function getIterator(): \Traversable
    {
        $this->used();

        return new \EmptyIterator();
    }
}
