<?php declare(strict_types=1);

namespace TH\Maybe\Option;

use TH\Maybe\Option;
use TH\Maybe\Result;

/**
 * @implements Option<never>
 * @immutable
 * @nodoc
 */
enum None implements Option
{
    case instance;

    /**
     * Returns `false`.
     *
     * @return false
     */
    public function isSome(): bool
    {
        return false;
    }

    /**
     * Returns `true`.
     *
     * @return true
     */
    public function isNone(): bool
    {
        return true;
    }

    /**
     * Returns `false` without calling `$predicate`.
     *
     * @return false
     */
    public function isSomeAnd(callable $predicate): bool
    {
        return false;
    }

    /**
     * Throws `new \RuntimeException($message)`.
     *
     * @throws \RuntimeException
     */
    public function expect(string $message): never
    {
        throw new \RuntimeException($message);
    }

    /**
     * Throws `new \RuntimeException("Unwrapping a `None` value")`.
     *
     * @throws \RuntimeException
     */
    public function unwrap(): never
    {
        $this->expect("Unwrapping a `None` value");
    }

    /**
     * Returns `$default`.
     *
     * @template U
     * @param U $default
     * @return U
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    /**
     * Returns `$default()`.
     *
     * @template U
     * @param callable():U $default
     * @return U
     */
    public function unwrapOrElse(callable $default): mixed
    {
        return $default();
    }

    /**
     * Returns `None` without calling `$callback`.
     *
     * @return $this
     */
    public function inspect(callable $callback): self
    {
        return $this;
    }

    /**
     * Returns `None`.
     *
     * @return $this
     */
    public function and(Option $right): Option
    {
        return $this;
    }

    /**
     * Returns `None` without calling `$right`.
     *
     * @return $this
     */
    public function andThen(callable $right): Option
    {
        return $this;
    }

    /**
     * Returns `$right`.
     */
    public function or(Option $right): Option
    {
        return $right;
    }

    /**
     * Returns `$right()`.
     */
    public function orElse(callable $right): Option
    {
        return $right();
    }

    /**
     * Returns `$right`.
     */
    public function xor(Option $right): Option
    {
        return $right;
    }

    /**
     * Returns `false`.
     *
     * @return false
     */
    public function contains(mixed $value, bool $strict = true): bool
    {
        return false;
    }
    /**
     * Returns `None` without calling `$predicate`.
     *
     * @return $this
     */
    public function filter(callable $predicate): Option
    {
        return $this;
    }

    /**
     * Returns `None`.
     *
     * @return $this
     */
    public function map(callable $callback): Option
    {
        return $this;
    }

    /**
     * Returns `$default` without calling `$callback`.
     */
    public function mapOr(callable $callback, mixed $default): mixed
    {
        return $default;
    }

    /**
     * Returns `$default()` without calling `$callback`.
     */
    public function mapOrElse(callable $callback, callable $default): mixed
    {
        return $default();
    }

    /**
     * Returns `None`.
     *
     * @return $this
     */
    public function zip(Option $option): self
    {
        return $this;
    }

    /**
     * Returns `Result\err($err)`.
     */
    public function okOr(mixed $err): Result\Err
    {
        return Result\err($err);
    }

    /**
     * Returns `Result\err($err())`.
     */
    public function okOrElse(callable $err): Result\Err
    {
        return Result\err($err());
    }

    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
