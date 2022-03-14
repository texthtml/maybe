<?php declare(strict_types=1);

namespace TH\Maybe\Option;

use TH\Maybe\Option;

/**
 * @template T
 */
interface Some
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
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * @return T
     */
    public function unwrapOrElse(callable $default): mixed;

    /**
     * @return $this
     */
    public function inspect(callable $callback): self;

    /**
     * @template U
     * @param Option<U> $right
     * @return Option<U>
     */
    public function and(Option $right): Option;

    /**
     * @template U
     * @param callable(T):Option<U> $right
     * @return Option<U>
     */
    public function andThen(callable $right): Option;

    /**
     * @param Option<T> $right
     * @return Option<T> & $this
     */
    public function or(Option $right): Option;

    /**
     * @param callable():Option<T> $right
     * @return Option<T> & $this
     */
    public function orElse(callable $right): Option;

    /**
     * @param Option<T> $right
     * @return Option<T>
     */
    public function xor(Option $right): Option;

    public function contains(mixed $value, bool $strict = true): bool;

    /**
     * @param callable(T):bool $predicate
     * @return Option<T>
     */
    public function filter(callable $predicate): Option;

    /**
     * @template U
     * @param callable(T):U $callback
     * @return Option<U> & Option\Some<U>
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
     * @return Option<array{T, U}>
     */
    public function zip(Option $option): Option;
}
