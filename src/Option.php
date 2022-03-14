<?php declare(strict_types=1);

namespace TH\Maybe;

/**
 * Type `Option` represents an optional value: every `Option` is either `Some`
 * and contains a value, or `None`, and does not.
 *
 * @template T
 * @immutable
 * @implements \IteratorAggregate<T>
 */
abstract class Option implements \IteratorAggregate
{
    final private function __construct() {}

    /**
     * Return a `None` option
     *
     * @return Option<never> & Option\None<never>
     */
    public static function none(): Option & Option\None
    {
        static $none;

        /**
         * @extends Option<never>
         * @implements Option\None
         */
        $none ??= new class () extends Option implements Option\None
        {
            public function expect(string $message): never
            {
                throw new \RuntimeException($message);
            }

            public function unwrap(): never
            {
                $this->expect("Unwrapping a `None` value");
            }
        };

        return $none;
    }

    /**
     * Return a `Some` option containing `$value`
     *
     * @template U
     * @param U $value
     * @return Option<U> & Option\Some<U>
     */
    public static function some(mixed $value): Option & Option\Some
    {
        /**
         * @extends Option<U>
         * @implements Option\Some<U>
         */
        $some = new class () extends Option implements Option\Some {
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

            public function and(Option $right): Option
            {
                return $right;
            }

            /**
             * @template V
             * @param callable(U):Option<V> $right
             * @return Option<V>
             */
            public function andThen(callable $right): Option
            {
                return $right($this->value);
            }

            /**
             * @param Option<U> $right
             * @return $this
             */
            public function or(Option $right): Option
            {
                return $this;
            }

            /**
             * @param callable():Option<U> $right
             * @return $this
             */
            public function orElse(callable $right): Option
            {
                return $this;
            }

            /**
             * @param Option<U> $right
             * @return Option<U>
             */
            public function xor(Option $right): Option
            {
                return $right instanceof Option\None
                    ? $this
                    : Option::none();
            }

            public function contains(mixed $value, bool $strict = true): bool
            {
                return $strict
                    ? ($this->value === $value)
                    // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
                    : ($this->value == $value);
            }

            /**
             * @param callable(U):bool $predicate
             * @return Option<U>
             */
            public function filter(callable $predicate): Option
            {
                return $predicate($this->value)
                    ? $this
                    : Option::none();
            }

            /**
             * @template V
             * @param callable(U):V $callback
             * @return Option<V> & Option\Some<V>
             */
            public function map(callable $callback): Option
            {
                return Option::some($callback($this->value));
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

            /**
             * @template V
             * @param Option<V> $option
             * @return Option<array{U, V}>
             */
            public function zip(Option $option): Option
            {
                // @phpstan-ignore-next-line zip() should return Option<array{U, V}> but returns Option<array{U, mixed}>
                return $option->map(
                    /**
                     * @param V $value
                     * @return array{U, V}
                     */
                    fn (mixed $value): array => [$this->value, $value],
                );
            }

            public function getIterator(): \Traversable
            {
                yield $this->value;
            }
        };

        $some->value = $value;

        return $some;
    }

    /**
     * Transform a value into an Option.
     * It will be a Some option containing $value if $value is different from $noneValue (default `null`)
     *
     * @template U
     * @param U $value
     * @return Option<U>
     */
    public static function fromValue(mixed $value, mixed $noneValue = null, bool $strict = true): Option
    {
        $same = $strict
            ? ($value === $noneValue)
            // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
            : ($value == $noneValue);

        return $same
            ? Option::none()
            : Option::some($value);
    }

    /**
     * Converts from `Option<Option<T>>` to `Option<T>`.
     *
     * @template U
     * @param Option<Option<U>> $option
     * @return Option<U>
     */
    public static function flatten(Option $option): Option
    {
        /** @var Option<U> $none */
        $none = Option::none();

        return $option instanceof Option\None
            ? $none
            : $option->unwrap();
    }

    /**
     * Unzips an option containing a tuple of two options.
     *
     * If `self` is `Some([a, b])` this method returns `[Some(a), Some(b)]`. Otherwise, `[None, None]` is returned.
     *
     * @template U
     * @template V
     * @param Option<array{U, V}> $option
     * @return array{Option<U>, Option<V>}
     */
    public static function unzip(Option $option): array
    {
        if ($option instanceof Option\None) {
            /** @var array{Option<U>, Option<V>} $noneNone */
            $noneNone = [Option::none(), Option::none()];

            return $noneNone;
        }

        [$left, $right] = $option->unwrap();

        return [Option::some($left), Option::some($right)];
    }

    /**
     * Extract the contained value in an `Option<T>` when it is the `Some` variant.
     * Throw a `RuntimeException` with a custum provided message if the `Option` is `None`.
     *
     * @return T
     * @throw \RuntimeException
     */
    abstract public function expect(string $message): mixed;

    /**
     * Extract the contained value in an `Option<T>` when it is the `Some` variant.
     * Throw a `RuntimeException` with a generic message if the `Option` is `None`.
     *
     * @return T
     * @throw \RuntimeException
     */
    abstract public function unwrap(): mixed;

    /**
     * Extract the contained value in an `Option<T>` when it is the `Some` variant.
     * Or `$default` if the `Option` is `None`.
     *
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    /**
     * Returns the contained `Some` value or computes it from a closure.
     *
     * @param callable():T $default
     * @return T
     */
    public function unwrapOrElse(callable $default): mixed
    {
        return $default();
    }

    /**
     * Calls the provided closure with a reference to the contained value (if `Some`).
     *
     * @param callable(T):mixed $callback
     * @return $this
     */
    public function inspect(callable $callback): self
    {
        return $this;
    }

    /**
     * Returns `None` if the option is `None`, otherwise returns `$right`.
     *
     * @template U
     * @param Option<U> $right
     * @return Option<U>
     */
    public function and(Option $right): self
    {
        return $this;
    }

    /**
     * Returns `None` if the option is `None`, otherwise calls `$right` with the wrapped value and returns the result.
     *
     * @template U
     * @param callable(T):Option<U> $right
     * @return Option<U>
     */
    public function andThen(callable $right): Option
    {
        return $this;
    }

    /**
     * Returns the option if it contains a value, otherwise returns `$right`.
     *
     * @param Option<T> $right
     * @return Option<T>
     */
    public function or(Option $right): self
    {
        return $right;
    }

    /**
     * Returns the option if it contains a value, otherwise calls `$right` and returns the result.
     *
     * @param callable():Option<T> $right
     * @return Option<T>
     */
    public function orElse(callable $right): Option
    {
        return $right();
    }

    /**
     * Returns the option if it is `Some`, otherwise returns `$right`.
     *
     * @param Option<T> $right
     * @return Option<T>
     */
    public function xor(Option $right): self
    {
        return $right;
    }

    /**
     * Returns true if the option is a `Some` value containing the given value.
     */
    public function contains(mixed $value, bool $strict = true): bool
    {
        return false;
    }

    /**
     * Returns `None` if the option is `None`, otherwise calls `$predicate` with the wrapped value and returns:
     *  * `Some(t)` if `$predicate` returns `true` (where `t` is the wrapped value), and
     *  * `None` if predicate returns `false`.
     *
     * @param callable(T):bool $predicate
     * @return Option<T>
     */
    public function filter(callable $predicate): Option
    {
        return $this;
    }

    /**
     * Maps an `Option<T>` to `Option<U>` by applying a function to a contained value.
     *
     * @template U
     * @param callable(T):U $callback
     * @return Option<U>
     */
    public function map(callable $callback): Option
    {
        return $this;
    }

    /**
     * Returns the provided default result (if `None`), or applies a function to
     * the contained value (if `Some`).
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
     * Computes a default function result (if `None`), or applies a different
     * function to the contained value (if `Some`).
     *
     * @template U
     * @param callable(T):U $callback
     * @param callable():U $default
     * @return U
     */
    public function mapOrElse(callable $callback, callable $default): mixed
    {
        return $default();
    }

    /**
     * Zips `self` with another `Option`.
     *
     * If `self` is `Some(s)` and other is `Some(o)`, this method returns `Some([s, o])`. Otherwise, `None` is returned.
     *
     * @template U
     * @param Option<U> $option
     * @return Option<array{T, U}>
     */
    public function zip(Option $option): Option
    {
        return $this;
    }

    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}
