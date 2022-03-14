<?php declare(strict_types=1);

namespace TH\Maybe;

/**
 * `Result` is a type that represents either success (`Ok`) or failure (`Err`).
 *
 * @template T
 * @template E
 * @immutable
 * @implements \IteratorAggregate<T>
 */
abstract class Result implements \IteratorAggregate
{
    final private function __construct() {}

    /**
     * Return a `Err` result
     *
     * @template F
     * @param F $value
     * @return Result<never, F> & Result\Err<never, F>
     */
    public static function err(mixed $value): Result & Result\Err
    {
        /**
         * @extends Result<never, F>
         * @implements Result\Err<never, F>
         */
        $err = new class () extends Result implements Result\Err
        {
            /**
             * @var F
             */
            protected mixed $value;

            /**
             * @throw \RuntimeException
             */
            public function expect(string $message): never
            {
                if ($this->value instanceof \Throwable) {
                    throw new \RuntimeException($message, previous: $this->value);
                }

                throw new \RuntimeException(\sprintf($message, \serialize($this->value)));
            }

            public function unwrap(): never
            {
                if ($this->value instanceof \Throwable) {
                    throw $this->value;
                }

                $this->expect("Unwrapping `Err`: %s");
            }

            public function unwrapErr(): mixed
            {
                return $this->value;
            }

            /**
             * @template T
             * @param callable(F):T $default
             * @return T
             */
            public function unwrapOrElse(callable $default): mixed
            {
                return $default($this->value);
            }

            /**
             * @return $this
             */
            public function inspectErr(callable $callback): self
            {
                $callback($this->value);

                return $this;
            }

            public function containsErr(mixed $value, bool $strict = true): bool
            {
                return $strict
                    ? ($this->value === $value)
                    // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
                    : ($this->value == $value);
            }

            /**
             * @template G
             * @param callable(F):G $callback
             * @return Result<never, G> & Result\Err<never, G>
             */
            public function mapErr(callable $callback): Result
            {
                /** @var Result<never, G> & Result\Err<never, G> */
                return Result::err($callback($this->value));
            }

            /**
             * @template U
             * @template G
             * @param callable(F):Result<U, G> $right
             * @return Result<U, G>
             */
            public function orElse(callable $right): Result
            {
                return $right($this->value);
            }

            /**
             * @template U
             * @template V
             * @param callable(U):V $callback
             * @param callable(F):V $default
             * @return V
             */
            public function mapOrElse(callable $callback, callable $default): mixed
            {
                return $default($this->value);
            }
        };

        $err->value = $value;

        return $err;
    }

    /**
     * Return a `Ok` Result containing `$value`
     *
     * @template U
     * @param U $value
     * @return Result<U, never> & Result\Ok<U, never>
     */
    public static function ok(mixed $value): Result & Result\Ok
    {
        /**
         * @extends Result<U, F>
         * @implements Result\Ok<U, F>
         */
        $ok = new class () extends Result implements Result\Ok {
            /**
             * @var U
             */
            protected mixed $value;

            public function expect(string $message): mixed
            {
                return $this->value;
            }

            public function unwrap(): mixed
            {
                return $this->value;
            }

            /**
             * @throw \RuntimeException
             */
            public function unwrapErr(): never
            {
                throw new \RuntimeException(\sprintf("Unwrapping err on `Ok`: %s", \serialize($this->value)));
            }

            public function unwrapOr(mixed $default): mixed
            {
                return $this->value;
            }

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

            public function and(Result $right): Result
            {
                return $right;
            }

            /**
             * @template V
             * @param callable(U):Result<V, E> $right
             * @return Result<V, E>
             */
            public function andThen(callable $right): Result
            {
                return $right($this->value);
            }

            /**
             * @template F
             * @param Result<U, F> $right
             * @return $this
             */
            public function or(Result $right): Result
            {
                return $this;
            }

            /**
             * @template F
             * @param callable(E):Result<U, F> $right
             * @return $this
             */
            public function orElse(callable $right): Result
            {
                return $this;
            }

            public function contains(mixed $value, bool $strict = true): bool
            {
                return $strict
                    ? ($this->value === $value)
                    // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
                    : ($this->value == $value);
            }

            public function containsErr(mixed $value, bool $strict = true): bool
            {
                return false;
            }

            /**
             * @template V
             * @param callable(U):V $callback
             * @return Result<V, E> & Result\Ok<V, E>
             */
            public function map(callable $callback): Result
            {
                /** @var Result<V, E> & Result\Ok<V, E> */
                return Result::ok($callback($this->value));
            }

            /**
             * @template F
             * @param callable(E):F $callback
             * @return Result<U, F> & Result\Ok<U, F>
             */
            public function mapErr(callable $callback): Result
            {
                /** @var Result<U, F> & Result\Ok<U, F> */
                return $this;
            }

            /**
             * @template V
             * @param callable(U):V $callback
             * @param V $default
             * @return V
             */
            public function mapOr(callable $callback, mixed $default): mixed
            {
                return $callback($this->value);
            }

            /**
             * @template V
             * @param callable(U):V $callback
             * @param V $default
             * @return V
             */
            public function mapOrElse(callable $callback, mixed $default): mixed
            {
                return $callback($this->value);
            }

            public function getIterator(): \Traversable
            {
                yield $this->value;
            }
        };

        $ok->value = $value;

        return $ok;
    }

    /**
     * Converts from `Result<Result<T, E>, E>` to `Result<T, E>`.
     *
     * @template U
     * @template F
     * @param Result<Result<U, F>, F> $result
     * @return Result<U, F>
     */
    public static function flatten(Result $result): Result
    {
        if ($result instanceof Result\Err) {
            /** @var Result<U, F> */
            return $result;
        }

        return $result->unwrap();
    }

    /**
     * Transposes a `Result` of an `Option` into an `Option` of a `Result`.
     *
     * `Ok(None)` will be mapped to `None`.
     * `Ok(Some(_))` and `Err(_)` will be mapped to `Some(Ok(_))` and `Some(Err(_))`.
     *
     * @template U
     * @template F
     * @param Result<Option<U>, F> $result
     * @return Option<Result<U, F>>
     */
    public static function transpose(Result $result): Option
    {
        if ($result instanceof Result\Err) {
            /** @var Option<Result<U, F>> */
            return Option::some($result);
        }

        /** @var Option<Result<U, F>> */
        return $result->unwrap()->map(Result::ok(...));
    }

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Throw a `RuntimeException` with a custum provided message if the `Result` is `Err`.
     *
     * @return T
     * @throw \RuntimeException
     */
    abstract public function expect(string $message): mixed;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Throw a `RuntimeException` with a generic message if the `Result` is `Err` or the contained err value
     * if it's a `\Throwable`
     *
     * @return T
     * @throw \Throwable
     */
    abstract public function unwrap(): mixed;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Err` variant.
     * Throw a `RuntimeException` with a generic message if the `Result` is `Ok`.
     *
     * @return E
     * @throw \RuntimeException
     */
    abstract public function unwrapErr(): mixed;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Or `$default` if the `Result` is `Err`.
     *
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    /**
     * Returns the contained `Ok` value or computes it from a closure.
     *
     * @param callable(E):T $default
     * @return T
     */
    abstract public function unwrapOrElse(callable $default): mixed;

    /**
     * Calls the provided closure with a reference to the contained value (if `Ok`).
     *
     * @param callable(T):mixed $callback
     * @return $this
     */
    public function inspect(callable $callback): self
    {
        return $this;
    }

    /**
     * Calls the provided closure with a reference to the contained value (if `Err`).
     *
     * @param callable(E):mixed $callback
     * @return $this
     */
    public function inspectErr(callable $callback): self
    {
        return $this;
    }

    /**
     * Returns `$right` if the Result is `Ok`, otherwise returns `$this`.
     *
     * @template U
     * @param Result<U, E> $right
     * @return Result<U, E>
     */
    public function and(Result $right): self
    {
        return $this;
    }

    /**
     * Returns `Err` if the Result is `Err`, otherwise calls `$right` with the wrapped value and returns the result.
     *
     * @template U
     * @template F
     * @param callable(T):Result<U, F> $right
     * @return Result<U, E|F>
     */
    public function andThen(callable $right): Result
    {
        return $this;
    }

    /**
     * Returns the Result if it contains a value, otherwise returns `$right`.
     *
     * @template F
     * @param Result<T, F> $right
     * @return Result<T, F>
     */
    public function or(Result $right): self
    {
        return $right;
    }

    /**
     * Returns the Result if it contains a value, otherwise calls `$right` and returns the result.
     *
     * @template F
     * @param callable(E):Result<T, F> $right
     * @return Result<T, F>
     */
    abstract public function orElse(callable $right): Result;

    /**
     * Returns true if the Result is a `Ok` value containing the given value.
     */
    public function contains(mixed $value, bool $strict = true): bool
    {
        return false;
    }

    /**
     * Returns true if the Result is a `Ok` value containing the given value.
     */
    abstract public function containsErr(mixed $value, bool $strict = true): bool;

    /**
     * Maps an `Result<T, E>` to `Result<U, E>` by applying a function to a contained `Ok` value.
     *
     * @template U
     * @param callable(T):U $callback
     * @return Result<U, E>
     */
    public function map(callable $callback): Result
    {
        return $this;
    }

    /**
     * Maps an `Result<T, E>` to `Result<T, F>` by applying a function to a contained `Err` value.
     *
     * @template F
     * @param callable(E):F $callback
     * @return Result<T, F>
     */
    abstract public function mapErr(callable $callback): Result;

    /**
     * Returns the provided default result (if `Err`), or applies a function to
     * the contained value (if `Ok`).
     *
     * @template U
     * @param callable(T):U $callback
     * @param U $default
     * @return U
     */
    public function mapOr(callable $callback, mixed $default): mixed
    {
        return $default;
    }

    /**
     * Converts from `Result<T, E>` to `Option<T>`, discarding the error, if any.
     *
     * @return Option<T>
     */
    public function extractOk(): Option
    {
        /** @var Option<T> */
        return $this->mapOrElse(
            Option::some(...),
            Option::none(...),
        );
    }

    /**
     * Converts from `Result<T, E>` to `Option<E>`, discarding the success value, if any.
     *
     * @return Option<E>
     */
    public function extractErr(): Option
    {
        /** @var Option<E> */
        return $this->mapOrElse(
            Option::none(...),
            Option::some(...),
        );
    }

    /**
     * Computes a default function result (if `Err`), or applies a different
     * function to the contained value (if `Ok`).
     *
     * @template U
     * @param callable(T):U $callback
     * @param callable(E):U $default
     * @return U
     */
    abstract public function mapOrElse(callable $callback, callable $default): mixed;

    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
