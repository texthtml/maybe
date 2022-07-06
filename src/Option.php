<?php declare(strict_types=1);

namespace TH\Maybe;

/**
 * Type `Option` represents an optional value: every `Option` is either `Some`
 * and contains a value, or `None`, and does not.
 *
 * # Examples
 *
 * ```
 * use TH\Maybe\Option;
 *
 * // @return Option<float>
 * function divide(float $numerator, float $denominator): Option {
 *   if ($denominator === 0.0) {
 *     return Option\none();
 *   }
 *
 *   return Option\some($numerator / $denominator);
 * }
 *
 * // The return value of the function is an option
 * $result = divide(2.0, 3.0);
 *
 * // Use instanceof to differentiate between Some & None
 * if ($result instanceof Option\Some) {
 *   // The division was valid
 *   echo "Result: {$result->unwrap()}";
 * } else {
 *   echo "Cannot divide by 0";
 * }
 * ```
 *
 * @template T
 * @extends \IteratorAggregate<T>
 * @immutable
 */
#[\TH\Maybe\Tests\Attributes\ExamplesSetup(\TH\Maybe\Tests\Helpers\IgnoreUnusedResults::class)]
interface Option extends \IteratorAggregate
{
    /**
     * Extract the contained value in an `Option<T>` when it is the `Some` variant.
     * Throw a `RuntimeException` with a custum provided message if the `Option` is `None`.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $x = Option\some("value");
     * assert($x->expect("fruits are healthy") === "value");
     * ```
     *
     * ```
     * use TH\Maybe\Option;
     *
     * // @var Option<string> $x
     * $x = Option\none();
     *
     * try {
     *   $x->expect("fruits are healthy");
     *   assert(false);
     * } catch (\RuntimeException $e) {
     *   assert($e->getMessage() === "fruits are healthy");
     * }
     * ```
     *
     * @return T
     * @throws \RuntimeException
     */
    public function expect(string $message): mixed;

    /**
     * Extract the contained value in an `Option<T>` when it is the `Some` variant.
     * Throw a `RuntimeException` with a generic message if the `Option` is `None`.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $x = Option\some("value");
     * assert($x->unwrap() === "value");
     * ```
     *
     * ```
     * use TH\Maybe\Option;
     *
     * // @var Option<string> $x
     * $x = Option\none();
     *
     * try {
     *   $x->unwrap();
     *   assert(false);
     * } catch (\RuntimeException) {}
     * ```
     *
     * @return T
     * @throws \RuntimeException
     */
    public function unwrap(): mixed;

    /**
     * Extract the contained value in an `Option<T>` when it is the `Some` variant.
     * Or `$default` if the `Option` is `None`.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * assert(Option\some("car")->unwrapOr("bike") === "car");
     * assert(Option\none()->unwrapOr("bike") === "bike");
     * ```
     *
     * @param T $default
     * @return T
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * Returns the contained `Some` value or computes it from a closure.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $k = 10;
     * assert(Option\some(4)->unwrapOrElse(fn () => 2 * $k) === 4);
     * assert(Option\none()->unwrapOrElse(fn () => 2 * $k) === 20);
     * ```
     *
     * @param callable():T $default
     * @return T
     */
    public function unwrapOrElse(callable $default): mixed;

    /**
     * Calls the provided closure with a reference to the contained value (if `Some`)
     * and (always) returns the same option.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $option = Option\some(4);
     * assert($option->inspect(fn (int $n) => printf("got: %d", $n)) === $option); // prints "got: 4"
     * // @var Option<int> $option
     * $option = Option\none();
     * assert($option->inspect(fn (int $n) => printf("%d", $n)) === $option); // prints nothing
     * ```
     *
     * @param callable(T):mixed $callback
     * @return $this
     */
    public function inspect(callable $callback): self;

    /**
     * Returns `None` if the option is `None`, otherwise returns `$right`.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $x = Option\some(2);
     * // @var Option<string> $y
     * $y = Option\none();
     * assert($x->and($y) === Option\none());
     * // @var Option<string> $x
     * $x = Option\none();
     * $y = Option\some("foo");
     * assert($x->and($y) === Option\none());
     * $x = Option\some(2);
     * $y = Option\some("foo");
     * assert($x->and($y) == Option\some("foo"));
     * // @var Option<string> $x
     * $x = Option\none();
     * // @var Option<string> $y
     * $y = Option\none();
     * assert($x->and($y) === Option\none());
     * ```
     *
     * @template U
     * @param Option<U> $right
     * @return Option<U>
     */
    public function and(Option $right): Option;

    /**
     * Returns `None` if the option is `None`, otherwise calls `$right` with the wrapped value and returns the result.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * // @return Option<int>
     * function to_exact_int(float $f): Option {
     *   $i = (int) $f;
     *   return ((float) $i) === $f ? Option\some($i) : Option\none();
     * }
     *
     * assert(Option\some(2.0)->andThen(to_exact_int(...)) == Option\some(2));
     * assert(Option\some(1.2)->andThen(to_exact_int(...)) === Option\none());
     * assert(Option\none()->andThen(to_exact_int(...)) === Option\none());
     * ```
     *
     * @template U
     * @param callable(T):Option<U> $right
     * @return Option<U>
     */
    public function andThen(callable $right): Option;

    /**
     * Returns the option if it contains a value, otherwise returns `$right`.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $x = Option\some(2);
     * // @var Option<int> $y
     * $y = Option\none();
     * assert($x->or($y) == Option\some(2));
     *
     * // @var Option<int> $x
     * $x = Option\none();
     * $y = Option\some(100);
     * assert($x->or($y) == Option\some(100));
     *
     * $x = Option\some(2);
     * $y = Option\some(100);
     * assert($x->or($y) == Option\some(2));
     *
     * // @var Option<int> $x
     * $x = Option\none();
     * // @var Option<int> $y
     * $y = Option\none();
     * assert($x->or($y) === Option\none());
     * ```
     *
     * @param Option<T> $right
     * @return Option<T>
     */
    public function or(Option $right): Option;

    /**
     * Returns the option if it contains a value, otherwise calls `$right` and returns the result.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * // @return Option<string>
     * function nobody(): Option {
     *   return Option\none();
     * }
     *
     * // @return Option<string>
     * function vikings(): Option {
     *   return Option\some("vikings");
     * }
     *
     * assert(Option\some("barbarians")->orElse(vikings(...)) == Option\some("barbarians"));
     * assert(Option\none()->orElse(vikings(...)) == Option\some("vikings"));
     * assert(Option\none()->orElse(nobody(...)) === Option\none());
     * ```
     *
     * @param callable():Option<T> $right
     * @return Option<T>
     */
    public function orElse(callable $right): Option;

    /**
     * Returns the option if it is `Some`, otherwise returns `$right`.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $x = Option\some(2);
     * // @var Option<int> $y
     * $y = Option\none();
     * assert($x->xor($y) == Option\some(2));
     * // @var Option<int> $x
     * $x = Option\none();
     * $y = Option\some(2);
     * assert($x->xor($y) == Option\some(2));
     * $x = Option\some(2);
     * $y = Option\some(2);
     * assert($x->xor($y) === Option\none());
     * // @var Option<int> $x
     * $x = Option\none();
     * // @var Option<int> $y
     * $y = Option\none();
     * assert($x->xor($y) === Option\none());
     * ```
     *
     * @param Option<T> $right
     * @return Option<T>
     */
    public function xor(Option $right): Option;

    /**
     * Returns true if the option is a `Some` value containing the given value.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $x = Option\some(2);
     * assert($x->contains(2) === true);
     * $x = Option\some(3);
     * assert($x->contains(2) === false);
     * // @var Option<int> $x
     * $x = Option\none();
     * assert($x->contains(2) === false);
     * ```
     */
    public function contains(mixed $value, bool $strict = true): bool;

    /**
     * Returns `None` if the option is `None`, otherwise calls `$predicate` with the wrapped value and returns:
     *  * `Some(t)` if `$predicate` returns `true` (where `t` is the wrapped value), and
     *  * `None` if predicate returns `false`.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $isEven = fn(int $n) => $n % 2 === 0;
     *
     * assert(Option\none()->filter($isEven) === Option\none());
     * assert(Option\some(3)->filter($isEven) === Option\none());
     * assert(Option\some(4)->filter($isEven) == Option\some(4));
     * ```
     *
     * @param callable(T):bool $predicate
     * @return Option<T>
     */
    public function filter(callable $predicate): Option;

    /**
     * Maps an `Option<T>` to `Option<U>` by applying a function to a contained value.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $maybeSomeString = Option\some("Hello, World!");
     * $maybeSomeLen = $maybeSomeString->map(strlen(...));
     * assert($maybeSomeLen == Option\some(13));
     * ```
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
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $x = Option\some("foo");
     * assert($x->mapOr(strlen(...), 42) === 3);
     * // @var Option<string> $x
     * $x = Option\none();
     * assert($x->mapOr(strlen(...), 42) ===42);
     * ```
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
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $k = 21;
     * $x = Option\some("foo");
     * assert($x->mapOrElse(strlen(...), fn () => 2 * $k) === 3);
     * // @var Option<string> $x
     * $x = Option\none();
     * assert($x->mapOrElse(strlen(...), fn () => 2 * $k) === 42);
     * ```
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
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * $x = Option\some(1);
     * $y = Option\some("hi");
     * // @var Option<int> $z
     * $z = Option\none();
     * assert($x->zip($y) == Option\some([1, "hi"]));
     * assert($x->zip($z) === Option\none());
     * ```
     *
     * @template U
     * @param Option<U> $option
     * @return Option<array{T, U}>
     */
    public function zip(Option $option): Option;

    /**
     * Transforms the `Option<T>` into a `Result<T, E>`, mapping `Some(v)` to `Ok(v)` and `None` to `Err(err)`.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\{Option, Result};
     *
     * $x = Option\some("foo")->okOr(0);
     * assert($x == $r = Result\ok("foo"));
     * $x->ok();
     * $r->ok();
     * // @var Option<string> $x
     * $x = Option\none()->okOr(0);
     * assert($x == $r = Result\err(0));
     * $x->err();
     * $r->err();
     * ```
     *
     * @template E
     * @param E $err
     * @return Result<T, E>
     */
    public function okOr(mixed $err): Result;

    /**
     * Transforms the `Option<T>` into a `Result<T, E>`, mapping `Some(v)` to `Ok(v)` and `None` to `Err(err())`.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\{Option, Result};
     *
     * $x = Option\some("foo")->okOrElse(fn () => 0);
     * assert($x == $r = Result\ok("foo"));
     * $x->ok();
     * $r->ok();
     * // @var Option<string> $x
     * $x = Option\none()->okOrElse(fn () => 0);
     * assert($x == $r = Result\err(0));
     * $x->err();
     * $r->err();
     * ```
     *
     * @template E
     * @param callable():E $err
     * @return Result<T, E>
     */
    public function okOrElse(callable $err): Result;
}
