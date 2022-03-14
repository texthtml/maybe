<?php declare(strict_types=1);

namespace TH\Maybe\Option;

use TH\Maybe\Option;

/**
 * @template T
 */
interface None
{
    /**
     * @throw \RuntimeException
     */
    public function expect(string $message): never;

    /**
     * @throw \RuntimeException
     */
    public function unwrap(): never;

    /**
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * @param callable():T $default
     * @return T
     */
    public function unwrapOrElse(callable $default): mixed;

    /**
     * @return Option<T> & $this
     */
    public function inspect(callable $callback): Option;

    /**
     * @template U
     * @param Option<U> $right
     * @return Option<U> & None<U>
     */
    public function and(Option $right): Option;

    /**
     * @template U
     * @param callable(T):Option<U> $right
     * @return Option<U> & $this
     */
    public function andThen(callable $right): Option;

    /**
     * @param Option<T> $right
     * @return Option<T>
     */
    public function or(Option $right): Option;

    /**
     * @param callable():Option<T> $right
     * @return Option<T>
     */
    public function orElse(callable $right): Option;

    /**
     * @param Option<T> $right
     * @return Option<T>
     */
    public function xor(Option $right): Option;

    /**
     * @return false
     */
    public function contains(mixed $value, bool $strict = true): bool;

    /**
     * @param callable(T):bool $predicate
     * @return Option<T> & $this
     */
    public function filter(callable $predicate): Option;

    /**
     * @template U
     * @param callable(T):U $callback
     * @return Option<U> & $this
     */
    public function map(callable $callback): Option;

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
     * @param callable():U $default
     * @return U
     */
    public function mapOrElse(callable $callback, callable $default): mixed;

    /**
     * @template U
     * @param Option<U> $option
     * @return Option<array{T, U}> & None<array{T, U}>
     */
    public function zip(Option $option): Option;
}
