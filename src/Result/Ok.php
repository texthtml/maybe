<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Result;

/**
 * @template T
 * @template E
 */
interface Ok
{
    /**
     * @return T
     */
    public function expect(string $message): mixed;

    /**
     * @return T
     */
    public function unwrap(): mixed;

    /**
     * @throw \RuntimeException
     */
    public function unwrapErr(): never;

    /**
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * @return T
     */
    public function unwrapOrElse(callable $default): mixed;

    /**
     * @return Result<T, E> & $this
     */
    public function inspect(callable $callback): Result;

    /**
     * @return Result<T, E> & $this
     */
    public function inspectErr(callable $callback): Result;

    /**
     * @template U
     * @param Result<U, E> $right
     * @return Result<U, E>
     */
    public function and(Result $right): Result;

    /**
     * @template U
     * @template F
     * @param callable(T):Result<U, F> $right
     * @return Result<U, E|F>
     */
    public function andThen(callable $right): Result;

    /**
     * @template F
     * @param Result<T, F> $right
     * @return Result<T, F> & $this
     */
    public function or(Result $right): Result;

    /**
     * @template F
     * @param callable(E):Result<T, F> $right
     * @return Result<T, F> & $this
     */
    public function orElse(callable $right): Result;

    public function contains(mixed $value, bool $strict = true): bool;

    /**
     * @return false
     */
    public function containsErr(mixed $value, bool $strict = true): bool;

    /**
     * @template U
     * @param callable(T):U $callback
     * @return Result<U, E> & Result\Ok<U, E>
     */
    public function map(callable $callback): Result;

    /**
     * @template F
     * @param callable(E):F $callback
     * @return Result<T, F> & Result\Ok<T, F>
     */
    public function mapErr(callable $callback): Result;

    /**
     * @template U
     * @param callable(T):U $callback
     * @param U $default
     * @return U
     */
    public function mapOr(callable $callback, mixed $default): mixed;

    /**
     * @template U
     * @param callable(T):U $callback
     * @param callable(E):U $default
     * @return U
     */
    public function mapOrElse(callable $callback, callable $default): mixed;
}
