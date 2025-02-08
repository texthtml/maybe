<?php declare(strict_types=1);

namespace TH\Maybe\Option;

use TH\Maybe\Option;
use TH\Maybe\Result;

/**
 * @template T
 * @implements Option<T>
 * @immutable
 * @nodoc
 */
final class Some implements Option
{
    /**
     * @param T $value
     * @nodoc
     */
    public function __construct(private mixed $value) {}

    /**
     * @return true
     */
    public function isSome(): bool
    {
        return true;
    }

    /**
     * @return false
     */
    public function isNone(): bool
    {
        return false;
    }

   /**
    * @param callable(T):bool $predicate
    */
    public function isSomeAnd(callable $predicate): bool
    {
        return $predicate($this->value);
    }

    /**
     * @phpstan-throws void
     */
    public function expect(string $message): mixed
    {
        return $this->value;
    }

    /**
     * @phpstan-throws void
     */
    public function unwrap(): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function unwrapOrElse(callable $default): mixed
    {
        return $this->value;
    }

    /**
     * @return $this
     */
    public function inspect(callable $callback): self
    {
        $callback($this->value);

        return $this;
    }

   /**
    * @template U
    * @param Option<U> $right
    * @return Option<U>
    */
    public function and(Option $right): Option
    {
        return $right;
    }

   /**
    * @template U
    * @param callable(T):Option<U> $right
    * @return Option<U>
    */
    public function andThen(callable $right): Option
    {
        return $right($this->value);
    }

    /**
     * @return $this
     */
    public function or(Option $right): Option
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function orElse(callable $right): Option
    {
        return $this;
    }

    public function xor(Option $right): Option
    {
        return $right instanceof Option\None
            ? $this
            : Option\none();
    }

    public function contains(mixed $value, bool $strict = true): bool
    {
        return $strict
            ? ($this->value === $value)
            // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
            : ($this->value == $value); // @phpstan-ignore equal.notAllowed
    }

    public function filter(callable $predicate): Option
    {
        return $predicate($this->value)
            ? $this
            : Option\none();
    }

   /**
    * @template U
    * @param callable(T):U $callback
    * @return Option<U>
    */
    public function map(callable $callback): Option
    {
        return Option\some($callback($this->value));
    }

   /**
    * @template U
    * @template V
    * @param callable(T):U $callback
    * @param V $default
    * @return U
    */
    public function mapOr(callable $callback, mixed $default): mixed
    {
        return $callback($this->value);
    }

   /**
    * @template U
    * @template V
    * @param callable(T):U $callback
    * @param callable():V $default
    * @return U
    */
    public function mapOrElse(callable $callback, callable $default): mixed
    {
        return $callback($this->value);
    }

    /**
     * @template U
     * @param Option<U> $option
     * @return Option<array{T, U}>
     * @psalm-suppress InvalidReturnType
     */
    public function zip(Option $option): Option
    {
        foreach ($option as $value) {
            return Option\some([$this->value, $value]);
        }

        return Option\none();
    }

    /**
     * @template U
     * @template V
     * @param Option<U> $option
     * @param callable(T, U):V $callback
     * @return (U is never ? Option\None : Option<V>)
     * @return Option<V>
     */
    public function zipWith(Option $option, callable $callback): Option
    {
        foreach ($option as $value) {
            return Option\some($callback($this->value, $value));
        }

        return Option\none();
    }

    /**
     * @template E
     * @param E $err
     * @return Result\Ok<T>
     */
    public function okOr(mixed $err): Result\Ok
    {
        return Result\ok($this->value);
    }

    /**
     * @template E
     * @param callable():E $err
     * @return Result\Ok<T>
     */
    public function okOrElse(callable $err): Result\Ok
    {
        return Result\ok($this->value);
    }

    public function getIterator(): \Traversable
    {
        yield $this->value;
    }
}
