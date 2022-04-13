<?php declare(strict_types=1);

namespace TH\Maybe;

/**
 * Type `Option` represents an optional value: every `Option` is either `Some`
 * and contains a value, or `None`, and does not.
 *
 * @template T
 * @extends \IteratorAggregate<T>
 * @immutable
 */
interface Option extends \IteratorAggregate
{
    /**
     * Extract the contained value in an `Option<T>` when it is the `Some` variant.
     * Throw a `RuntimeException` with a custum provided message if the `Option` is `None`.
     *
     * @return T
     * @throws \RuntimeException
     */
    public function expect(string $message): mixed;

    /**
     * Extract the contained value in an `Option<T>` when it is the `Some` variant.
     * Throw a `RuntimeException` with a generic message if the `Option` is `None`.
     *
     * @return T
     * @throws \RuntimeException
     */
    public function unwrap(): mixed;

    /**
     * Extract the contained value in an `Option<T>` when it is the `Some` variant.
     * Or `$default` if the `Option` is `None`.
     *
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * Returns the contained `Some` value or computes it from a closure.
     *
     * @param callable():T $default
     * @return T
     */
    public function unwrapOrElse(callable $default): mixed;

    /**
     * Calls the provided closure with a reference to the contained value (if `Some`).
     *
     * @param callable(T):mixed $callback
     * @return $this
     */
    public function inspect(callable $callback): self;

    /**
     * Returns `None` if the option is `None`, otherwise returns `$right`.
     *
     * @template U
     * @param Option<U> $right
     * @return Option<U>
     */
    public function and(Option $right): Option;

    /**
     * Returns `None` if the option is `None`, otherwise calls `$right` with the wrapped value and returns the result.
     *
     * @template U
     * @param callable(T):Option<U> $right
     * @return Option<U>
     */
    public function andThen(callable $right): Option;

    /**
     * Returns the option if it contains a value, otherwise returns `$right`.
     *
     * @param Option<T> $right
     * @return Option<T>
     */
    public function or(Option $right): Option;

    /**
     * Returns the option if it contains a value, otherwise calls `$right` and returns the result.
     *
     * @param callable():Option<T> $right
     * @return Option<T>
     */
    public function orElse(callable $right): Option;

    /**
     * Returns the option if it is `Some`, otherwise returns `$right`.
     *
     * @param Option<T> $right
     * @return Option<T>
     */
    public function xor(Option $right): Option;

    /**
     * Returns true if the option is a `Some` value containing the given value.
     */
    public function contains(mixed $value, bool $strict = true): bool;

    /**
     * Returns `None` if the option is `None`, otherwise calls `$predicate` with the wrapped value and returns:
     *  * `Some(t)` if `$predicate` returns `true` (where `t` is the wrapped value), and
     *  * `None` if predicate returns `false`.
     *
     * @param callable(T):bool $predicate
     * @return Option<T>
     */
    public function filter(callable $predicate): Option;

    /**
     * Maps an `Option<T>` to `Option<U>` by applying a function to a contained value.
     *
     * @template U
     * @param callable(T):U $callback
     * @return Option<U>
     */
    public function map(callable $callback): Option;

    /**
     * Returns the provided default result (if `None`), or applies a function to
     * the contained value (if `Some`).
     *
     * @template U
     * @param callable(T):U $callback
     * @param U $default
     * @return U
     */
    public function mapOr(callable $callback, mixed $default): mixed;

    /**
     * Computes a default function result (if `None`), or applies a different
     * function to the contained value (if `Some`).
     *
     * @template U
     * @param callable(T):U $callback
     * @param callable():U $default
     * @return U
     */
    public function mapOrElse(callable $callback, callable $default): mixed;

    /**
     * Zips `$this` with another `Option`.
     *
     * If `$this` is `Some(s)` and other is `Some(o)`, this method returns `Some([s, o])`.
     * Otherwise, `None` is returned.
     *
     * @template U
     * @param Option<U> $option
     * @return Option<array{T, U}>
     */
    public function zip(Option $option): Option;

    /**
     * Transforms the `Option<T>` into a `Result<T, E>`, mapping `Some(v)` to `Ok(v)` and `None` to `Err(err)`.
     *
     * @template E
     * @param E $err
     * @return Result<T, E>
     */
    public function okOr(mixed $err): Result;

    /**
     * Transforms the `Option<T>` into a `Result<T, E>`, mapping `Some(v)` to `Ok(v)` and `None` to `Err(err())`.
     *
     * @template E
     * @param callable():E $err
     * @return Result<T, E>
     */
    public function okOrElse(callable $err): Result;
}
