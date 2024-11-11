<?php declare(strict_types=1);

namespace TH\Maybe;

use TH\DocTest\Attributes\ExamplesSetup;
use TH\Maybe\Tests\Helpers\IgnoreUnusedResults;

/**
 * `Result<T, E>` is the type used for returning and propagating errors. It two
 * variants, `Ok(T)`, representing success and containing a value, and `Err(E)`,
 * representing error and containing an error value.
 *
 * Functions return `Result` whenever errors are expected and recoverable.
 *
 * A simple function returning Result might be defined and used like so:
 *
 * ```php
 * // @param Result<int,string>
 * function parse_version(string $header): Result {
 *     return match ($header[0] ?? null) {
 *         null => Result\err("invalid header length"),
 *         "1" => Result\ok(1),
 *         "2" => Result\ok(2),
 *         default => Result\err("invalid version"),
 *     };
 * }
 *
 * $version = parse_version("1.x");
 * if ($version->isOk()) {
 *     echo "working with version: {$version->unwrap()}";
 * } else {
 *     echo "error parsing header: {$version->unwrapErr()}";
 * }
 * // @prints working with version: 1
 * ```
 *
 * ### Results must be used
 *
 * A common problem with using return values to indicate errors is that it is easy
 * to ignore the return value, thus failing to handle the error. Unused `Result`s
 * are tracked and will trigger an exception when a `Result` value is ignored and
 * goes out of scope. This makes `Result` especially useful with functions that may
 * encounter errors but don’t otherwise return a useful value.
 *
 * ```php
 * // Write $data in $filepath
 * // @return Result<int,string> The number of bytes that were written to the file if Ok, an error message otherwise
 * function writeInFile(string $filepath, string $data): Result {
 *     $res = @file_put_contents($filepath, $data);
 *
 *     if ($res === false) {
 *         return Result\err("failed to write in $filepath");
 *     }
 *
 *     return Result\ok($res);
 * }
 *
 * writeInFile("/path/to/file", "Hi!");
 * // @throws TH\Maybe\Result\UnusedResultException Unused Result dropped
 * ```
 *
 * Using a `Result` can be done by calling any method on it, except `inspect()` & `inspectErr()`.
 *
 * Note: some methods return another `Result` that must also be used.
 *
 * @template T
 * @template E
 * @immutable
 * @extends \IteratorAggregate<T>
 */
interface Result extends \IteratorAggregate
{
    /**
     * Returns `true` if the result is the `Ok` variant.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * self::assertTrue($x->isOk());
     * ```
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\err(2);
     * self::assertFalse($x->isOk());
     * ```
     *
     * @psalm-assert-if-true Result\Ok<T> $this
     * @psalm-assert-if-false Result\Err<E> $this
     */
    public function isOk(): bool;

    /**
     * Returns `true` if the result is the `Err` variant.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * self::assertFalse($x->isErr());
     * ```
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\err(2);
     * self::assertTrue($x->isErr());
     * ```
     *
     * @psalm-assert-if-true Result\Err<E> $this
     * @psalm-assert-if-false Result\Ok<T> $this
     */
    public function isErr(): bool;

    /**
     * Returns `true` if the result is the `Ok` variant and the value inside of it matches a predicate.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * self::assertTrue($x->isOkAnd(fn ($n) => $n < 5));
     * self::assertFalse($x->isOkAnd(fn ($n) => $n > 5));
     * ```
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\err(2);
     * self::assertFalse($x->isOkAnd(fn ($n) => $n < 5));
     * self::assertFalse($x->isOkAnd(fn ($n) => $n > 5));
     * ```
     *
     * @param callable(T):bool $predicate
     * @psalm-assert-if-true Result\Ok<T> $this
     */
    public function isOkAnd(callable $predicate): bool;

    /**
     * Returns `true` if the result is the `Err` variant and the value inside of it matches a predicate.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\err(2);
     * self::assertTrue($x->isErrAnd(fn ($n) => $n < 5));
     * self::assertFalse($x->isErrAnd(fn ($n) => $n > 5));
     * ```
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * self::assertFalse($x->isErrAnd(fn ($n) => $n < 5));
     * self::assertFalse($x->isErrAnd(fn ($n) => $n > 5));
     * ```
     *
     * @param callable(E):bool $predicate
     * @psalm-assert-if-true Result\Err<E> $this
     */
    public function isErrAnd(callable $predicate): bool;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Throw a `RuntimeException` with a custum provided message if the `Result` is `Err`.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\err("emergency failure");
     * $x->expect("Testing expect"); // @throws RuntimeException Testing expect
     * ```
     *
     * @return T
     * @throws \RuntimeException
     * @psalm-assert Result\Ok<T> $this
     */
    public function expect(string $message): mixed;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Throw a `RuntimeException` with a generic message if the `Result` is `Err` or the contained err value
     * if it's a `\Throwable`
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\Ok(2);
     * self::assertEq($x->unwrap(), 2);
     * ```
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\err("emergency failure");
     * $x->unwrap(); // @throws RuntimeException Unwrapping `Err`: s:17:"emergency failure";
     * ```
     *
     * @return T
     * @throws \Throwable
     * @psalm-assert =Result\Ok<T> $this
     */
    public function unwrap(): mixed;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Err` variant.
     * Throw a `RuntimeException` with a generic message if the `Result` is `Ok`.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\Ok(2);
     * self::assertEq($x->unwrapErr(), 2); // @throws RuntimeException Unwrapping err on `Ok`: i:2;
     * ```
     *
     * @return E
     * @throws \RuntimeException
     * @psalm-assert =Result\Err<E> $this
     */
    public function unwrapErr(): mixed;

    /**
     * Extract the contained value in an `Result<T, E>` when it is the `Ok` variant.
     * Or `$default` if the `Result` is `Err`.
     *
     * # Examples
     *
     * ```
     * $default = 2;
     * // @var Result<int,string> $x
     * $x = Result\Ok(9);
     * self::assertEq($x->unwrapOr($default), 9);
     *
     * // @var Result<int,string> $x
     * $x = Result\err("emergency failure");
     * self::assertEq($x->unwrapOr($default), $default);
     * ```
     *
     * @template U
     * @param U $default
     * @return T|U
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * Returns the contained `Ok` value or computes it from a closure.
     *
     * # Examples
     *
     * ```
     * self::assertEq(Result\ok(2)->unwrapOrElse(strlen(...)), 2);
     * self::assertEq(Result\err("foo")->unwrapOrElse(strlen(...)), 3);
     * ```
     *
     * @template U
     * @param callable(E):U $default
     * @return T|U
     */
    public function unwrapOrElse(callable $default): mixed;

    /**
     * Calls the provided closure with a reference to the contained value (if `Ok`).
     *
     * # Examples
     *
     * ```
     * // @return Return<int,string>
     * function parseInt(string $number): Result {
     *     $int = (int) $number;
     *
     *     if ($number === (string) $int) return Result\ok($int);
     *
     *     return Result\err("could not parse `$number`");
     * }
     *
     * $i = parseInt("4")
     *     ->inspect(fn (int $x) => printf("original: %d", $x)) // @prints original: 4
     *     ->map(fn (int $x) => pow($x, 3))
     *     ->expect("failed to parse number");
     *
     * self::assertEq($i, 64);
     * ```
     *
     * @param callable(T):mixed $callback
     * @return $this
     */
    public function inspect(callable $callback): self;

    /**
     * Calls the provided closure with a reference to the contained value (if `Err`).
     *
     * # Examples
     *
     * ```
     * // @return Result<string,string>
     * function readFrom(string $filepath): Result {
     *     $data = @file_get_contents($filepath);
     *
     *     if ($data === false) {
     *         return Result\err("$filepath does not exist");
     *     }
     *
     *     return Result\ok($data);
     * }
     *
     * // @return Result<string,string>
     * function read(): Result {
     *     return readFrom("/not/a/file")
     *         ->inspectErr(fn ($e) => printf("failed to read file: %s", $e));
     * }
     *
     * read(); // @prints failed to read file: /not/a/file does not exist
     * ```
     *
     * @param callable(E):mixed $callback
     * @return $this
     */
    #[ExamplesSetup(IgnoreUnusedResults::class)]
    public function inspectErr(callable $callback): self;

    /**
     * Returns `$right` if the Result is `Ok`, otherwise returns `$this`.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * // @var Result<string,string> $y
     * $y = Result\err("late error");
     * self::assertEq($x->and($y), Result\err("late error"));
     *
     * // @var Result<int,string> $x
     * $x = Result\err("early error");
     * // @var Result<string,string> $y
     * $y = Result\ok("foo");
     * self::assertEq($x->and($y), Result\err("early error"));
     *
     * // @var Result<int,string> $x
     * $x = Result\err("not a 2");
     * // @var Result<string,string> $y
     * $y = Result\err("late error");
     * self::assertEq($x->and($y), Result\err("not a 2"));
     *
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * // @var Result<string,string> $y
     * $y = Result\ok("different result type");
     * self::assertEq($x->and($y), Result\ok("different result type"));
     * ```
     *
     * @template U
     * @param Result<U, E> $right
     * @return Result<U, E>
     */
    #[ExamplesSetup(IgnoreUnusedResults::class)]
    public function and(Result $right): Result;

    /**
     * Returns `Err` if the Result is `Err`, otherwise calls `$right` with the wrapped value and returns the result.
     *
     * Often used to chain fallible operations that may return `Err`.
     *
     * # Examples
     *
     * ```
     * // @return Result<int,string>
     * function square(int $x): Result {
     *     $x *= $x;
     *     if (is_int($x)) return Result\ok($x);
     *     return Result\err("overflowed");
     * }
     *
     * self::assertEq(Result\ok(2)->andThen(square(...)), Result\ok(4));
     * self::assertEq(Result\ok(10_000_000_000)->andThen(square(...)), Result\err("overflowed"));
     * self::assertEq(Result\err("not a number")->andThen(square(...)), Result\err("not a number"));
     * ```
     *
     * @template U
     * @template F
     * @param callable(T):Result<U, F> $right
     * @return Result<U, E|F>
     */
    #[ExamplesSetup(IgnoreUnusedResults::class)]
    public function andThen(callable $right): Result;

    /**
     * Returns the Result if it contains a value, otherwise returns `$right`.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * // @var Result<string,string> $y
     * $y = Result\err("late error");
     * self::assertEq($x->or($y), Result\ok(2));
     *
     * // @var Result<int,string> $x
     * $x = Result\err("early error");
     * // @var Result<int,string> $y
     * $y = Result\ok(2);
     * self::assertEq($x->or($y), Result\ok(2));
     *
     * // @var Result<int,string> $x
     * $x = Result\err("not a 2");
     * // @var Result<string,string> $y
     * $y = Result\err("late error");
     * self::assertEq($x->or($y), Result\err("late error"));
     *
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * // @var Result<string,string> $y
     * $y = Result\ok(100);
     * self::assertEq($x->or($y), Result\ok(2));
     * ```
     *
     * @template F
     * @param Result<T, F> $right
     * @return Result<T, F>
     */
    #[ExamplesSetup(IgnoreUnusedResults::class)]
    public function or(Result $right): Result;

    /**
     * Returns the Result if it contains a value, otherwise calls `$right` and returns the result.
     *
     * # Examples
     *
     * ```
     * // @return Result<int,int>
     * function sq(int $x): Result { return Result\ok($x * $x); }
     * // @return Result<int,int>
     * function err(int $x): Result { return Result\err($x); }
     *
     * self::assertEq(Result\ok(2)->orElse(sq(...))->orElse(sq(...)), Result\ok(2));
     * self::assertEq(Result\ok(2)->orElse(err(...))->orElse(sq(...)), Result\ok(2));
     * self::assertEq(Result\err(3)->orElse(sq(...))->orElse(err(...)), Result\ok(9));
     * self::assertEq(Result\err(3)->orElse(err(...))->orElse(err(...)), Result\err(3));
     * ```
     *
     * @template F
     * @param callable(E):Result<T, F> $right
     * @return Result<T, F>
     */
    #[ExamplesSetup(IgnoreUnusedResults::class)]
    public function orElse(callable $right): Result;

    /**
     * Returns true if the Result is a `Ok` value containing the given value.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * self::assertTrue($x->contains(2));
     *
     * // @var Result<int,string> $x
     * $x = Result\ok(3);
     * self::assertFalse($x->contains(2));
     *
     * // @var Result<int,string> $x
     * $x = Result\err("Some error message");
     * self::assertFalse($x->contains(2));
     * ```
     *
     * @psalm-assert-if-true Result\Ok<T> $this
     */
    public function contains(mixed $value, bool $strict = true): bool;

    /**
     * Returns true if the Result is a `Ok` value containing the given value.
     *
     * # Examples
     *
     * ```
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * self::assertFalse($x->containsErr("Some error message"));
     *
     * // @var Result<int,string> $x
     * $x = Result\err("Some error message");
     * self::assertTrue($x->containsErr("Some error message"));
     *
     * // @var Result<int,string> $x
     * $x = Result\err("Some other error message");
     * self::assertFalse($x->containsErr("Some error message"));
     * ```
     *
     * @psalm-assert-if-true Result\Err<E> $this
     */
    public function containsErr(mixed $value, bool $strict = true): bool;

    /**
     * Maps an `Result<T, E>` to `Result<U, E>` by applying a function to a contained `Ok` value.
     *
     * # Examples
     *
     * Print the numbers on each line of a string multiplied by two.
     *
     * ```
     * // @return Return<int,string>
     * function parseInt(string $number): Result {
     *     $int = (int) $number;
     *
     *     if ($number === (string) $int) return Result\ok($int);
     *
     *     return Result\err("could not parse `$number`");
     * }
     *
     * $input = "1\n2\n3\n4\n";
     *
     * foreach(explode(PHP_EOL, $input) as $num) {
     *     $n = parseInt($num)->map(fn ($i) => $i * 2);
     *
     *     if ($n->isOk()) {
     *         echo $n->unwrap(), PHP_EOL;
     *     }
     * }
     * // @prints 2
     * // @prints 4
     * // @prints 6
     * // @prints 8
     * ```
     *
     * @template U
     * @param callable(T):U $callback
     * @return Result<U, E>
     */
    #[ExamplesSetup(IgnoreUnusedResults::class)]
    public function map(callable $callback): Result;

    /**
     * Maps an `Result<T, E>` to `Result<T, F>` by applying a function to a contained `Err` value.
     *
     * # Examples
     *
     * ```
     * ```
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
     * # Examples
     *
     * ```
     * // @var Result<string,string> $x
     * $x = Result\ok("foo");
     * self::assertEq($x->mapOr(strlen(...), 42), 3);
     *
     * // @var Result<string,string> $x
     * $x = Result\err("bar");
     * self::assertEq($x->mapOr(strlen(...), 42), 42);
     * ```
     *
     * @template U
     * @param callable(T):U $callback
     * @param U $default
     * @return U
     */
    #[ExamplesSetup(IgnoreUnusedResults::class)]
    public function mapOr(callable $callback, mixed $default): mixed;

    /**
     * Converts from `Result<T, E>` to `Option<T>`, discarding the error, if any.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * self::assertEq($x->ok(), Option\some(2));
     *
     * // @var Result<int,string> $x
     * $x = Result\err("Nothing here");
     * self::assertEq($x->ok(), Option\none());
     * ```
     *
     * @return Option<T>
     */
    #[ExamplesSetup(IgnoreUnusedResults::class)]
    public function ok(): Option;

    /**
     * Converts from `Result<T, E>` to `Option<E>`, discarding the success value, if any.
     *
     * # Examples
     *
     * ```
     * use TH\Maybe\Option;
     *
     * // @var Result<int,string> $x
     * $x = Result\ok(2);
     * self::assertEq($x->err(), Option\none());
     *
     * // @var Result<int,string> $x
     * $x = Result\err("Nothing here");
     * self::assertEq($x->err(), Option\some("Nothing here"));
     * ```
     *
     * @return Option<E>
     */
    #[ExamplesSetup(IgnoreUnusedResults::class)]
    public function err(): Option;

    /**
     * Computes a default function result (if `Err`), or applies a different
     * function to the contained value (if `Ok`).
     *
     * This function can be used to unpack a successful result while handling an error.
     *
     * # Examples
     *
     * ```
     * $k = 21;
     *
     * // @var Result<string,string> $x
     * $x = Result\ok("foo");
     * self::assertEq($x->mapOrElse(strlen(...), fn ($e) => $k * 2), 3);
     *
     * // @var Result<string,string> $x
     * $x = Result\err("bar");
     * self::assertEq($x->mapOrElse(strlen(...), fn ($e) => $k * 2), 42);
     * ```
     *
     * @template U
     * @param callable(T):U $callback
     * @param callable(E):U $default
     * @return U
     */
    public function mapOrElse(callable $callback, callable $default): mixed;
}
