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
     * @return false
     */
    public function isSome(): bool
    {
        return false;
    }

    /**
     * @return true
     */
    public function isNone(): bool
    {
        return true;
    }

    /**
     * @return false
     */
    public function isSomeAnd(callable $predicate): bool
    {
        return false;
    }

    /**
     * @throws \RuntimeException
     */
    public function expect(string $message): never
    {
        throw new \RuntimeException($message);
    }

    /**
     * @throws \RuntimeException
     */
    public function unwrap(): never
    {
        $this->expect("Unwrapping a `None` value");
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    public function unwrapOrElse(callable $default): mixed
    {
        return $default();
    }

    /**
     * @return $this
     */
    public function inspect(callable $callback): self
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function and(Option $right): Option
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function andThen(callable $right): Option
    {
        return $this;
    }

    public function or(Option $right): Option
    {
        return $right;
    }

    public function orElse(callable $right): Option
    {
        return $right();
    }

    public function xor(Option $right): Option
    {
        return $right;
    }

    /**
     * @return false
     */
    public function contains(mixed $value, bool $strict = true): bool
    {
        return false;
    }

    /**
     * @return $this
     */
    public function filter(callable $predicate): Option
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function map(callable $callback): Option
    {
        return $this;
    }

    public function mapOr(callable $callback, mixed $default): mixed
    {
        return $default;
    }

    public function mapOrElse(callable $callback, callable $default): mixed
    {
        return $default();
    }

    /**
     * @template U
     * @param Option<U> $option
     * @return $this
     */
    public function zip(Option $option): self
    {
        return $this;
    }

    /**
     * @template E
     * @param E $err
     * @return Result\Err<E>
     */
    public function okOr(mixed $err): Result\Err
    {
        return Result\err($err);
    }

    /**
     * @template E
     * @param callable():E $err
     * @return Result\Err<E>
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
