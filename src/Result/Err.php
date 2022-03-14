<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Result;

/**
 * @template T
 * @template E
 */
interface Err
{
    /**
     * @throw \RuntimeException
     */
    public function expect(string $message): never;

    /**
     * @throw \Throwable
     */
    public function unwrap(): never;

    /**
     * @return E
     */
    public function unwrapErr(): mixed;

    /**
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * @param callable(E):T $default
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
     * @return Result<U, E> & Err<U, E>
     */
    public function and(Result $right): Result;

    /**
     * @template U
     * @template F
     * @param callable(T):Result<U, F> $right
     * @return Result<U, E|F> & $this
     */
    public function andThen(callable $right): Result;

    /**
     * @template F
     * @param Result<T, F> $right
     * @return Result<T, F>
     */
    public function or(Result $right): Result;

    /**
     * @template F
     * @param callable(E):Result<T, F> $right
     * @return Result<T, F>
     */
    public function orElse(callable $right): Result;

    /**
     * @return false
     */
    public function contains(mixed $value, bool $strict = true): bool;

    public function containsErr(mixed $value, bool $strict = true): bool;

    /**
     * @template U
     * @param callable(T):U $callback
     * @return Result<U, E> & $this
     */
    public function map(callable $callback): Result;

    /**
     * @template F
     * @param callable(E):F $callback
     * @return Result<T, F>
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
