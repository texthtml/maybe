<?php declare(strict_types=1);

namespace TH\Maybe;

/**
 * `Result` is a type that represents either success (`Ok`) or failure (`Err`).
 *
 * A `Result` must be used
 *
 * ```
 * use TH\Maybe\Result;
 *
 * Result\ok(42); // @throws TH\Maybe\Result\UnusedResultException Unused Result dropped
 * ```
 *
 * @template T
 * @template E
 * @immutable
 * @extends \IteratorAggregate<T>
 */
interface Result extends \IteratorAggregate
{
    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Throw a `RuntimeException` with a custum provided message if the `Result` is `Err`.
     *
     * @return T
     * @throws \RuntimeException
     */
    public function expect(string $message): mixed;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Throw a `RuntimeException` with a generic message if the `Result` is `Err` or the contained err value
     * if it's a `\Throwable`
     *
     * @return T
     * @throws \Throwable
     */
    public function unwrap(): mixed;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Err` variant.
     * Throw a `RuntimeException` with a generic message if the `Result` is `Ok`.
     *
     * @return E
     * @throws \RuntimeException
     */
    public function unwrapErr(): mixed;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Or `$default` if the `Result` is `Err`.
     *
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * Returns the contained `Ok` value or computes it from a closure.
     *
     * @param callable(E):T $default
     * @return T
     */
    public function unwrapOrElse(callable $default): mixed;

    /**
     * Calls the provided closure with a reference to the contained value (if `Ok`).
     *
     * @param callable(T):mixed $callback
     * @return $this
     */
    public function inspect(callable $callback): self;

    /**
     * Calls the provided closure with a reference to the contained value (if `Err`).
     *
     * @param callable(E):mixed $callback
     * @return $this
     */
    public function inspectErr(callable $callback): self;

    /**
     * Returns `$right` if the Result is `Ok`, otherwise returns `$this`.
     *
     * @template U
     * @param Result<U, E> $right
     * @return Result<U, E>
     */
    public function and(Result $right): Result;

    /**
     * Returns `Err` if the Result is `Err`, otherwise calls `$right` with the wrapped value and returns the result.
     *
     * @template U
     * @template F
     * @param callable(T):Result<U, F> $right
     * @return Result<U, E|F>
     */
    public function andThen(callable $right): Result;

    /**
     * Returns the Result if it contains a value, otherwise returns `$right`.
     *
     * @template F
     * @param Result<T, F> $right
     * @return Result<T, F>
     */
    public function or(Result $right): Result;

    /**
     * Returns the Result if it contains a value, otherwise calls `$right` and returns the result.
     *
     * @template F
     * @param callable(E):Result<T, F> $right
     * @return Result<T, F>
     */
    public function orElse(callable $right): Result;

    /**
     * Returns true if the Result is a `Ok` value containing the given value.
     */
    public function contains(mixed $value, bool $strict = true): bool;

    /**
     * Returns true if the Result is a `Ok` value containing the given value.
     */
    public function containsErr(mixed $value, bool $strict = true): bool;

    /**
     * Maps an `Result<T, E>` to `Result<U, E>` by applying a function to a contained `Ok` value.
     *
     * @template U
     * @param callable(T):U $callback
     * @return Result<U, E>
     */
    public function map(callable $callback): Result;

    /**
     * Maps an `Result<T, E>` to `Result<T, F>` by applying a function to a contained `Err` value.
     *
     * @template F
     * @param callable(E):F $callback
     * @return Result<T, F>
     */
    public function mapErr(callable $callback): Result;

    /**
     * Returns the provided default result (if `Err`), or applies a function to
     * the contained value (if `Ok`).
     *
     * @template U
     * @param callable(T):U $callback
     * @param U $default
     * @return U
     */
    public function mapOr(callable $callback, mixed $default): mixed;

    /**
     * Converts from `Result<T, E>` to `Option<T>`, discarding the error, if any.
     *
     * @return Option<T>
     */
    public function ok(): Option;

    /**
     * Converts from `Result<T, E>` to `Option<E>`, discarding the success value, if any.
     *
     * @return Option<E>
     */
    public function err(): Option;

    /**
     * Computes a default function result (if `Err`), or applies a different
     * function to the contained value (if `Ok`).
     *
     * @template U
     * @param callable(T):U $callback
     * @param callable(E):U $default
     * @return U
     */
    public function mapOrElse(callable $callback, callable $default): mixed;
}
