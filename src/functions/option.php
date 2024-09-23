<?php declare(strict_types=1);

namespace TH\Maybe\Option;

use TH\DocTest\Attributes\ExamplesSetup;
use TH\Maybe\Option;
use TH\Maybe\Result;
use TH\Maybe\Tests\Helpers\IgnoreUnusedResults;

/**
 * Return a `Option\Some` option containing `$value`.
 *
 * @template T
 * @param T $value
 * @return Option\Some<T>
 */
function some(mixed $value): Option\Some
{
    return new Option\Some($value);
}

/**
 * Return a `Option\None` option containing no values.
 */
function none(): Option\None
{
    return Option\None::instance;
}

/**
 * Transform a value into an `Option`.
 * It will be a `Some` option containing `$value` if `$value` is different from `$noneValue` (default `null`)
 *
 * # Examples
 *
 * ```
 * self::assertEq(Option\fromValue("fruits"), Option\some("fruits"));
 * self::assertEq(Option\fromValue(null), Option\none());
 * ```
 *
 * @template U
 * @param U $value
 * @return Option<U>
 */
function fromValue(mixed $value, mixed $noneValue = null, bool $strict = true): Option
{
    $same = $strict
        ? ($value === $noneValue)
        // @phpcs:ignore SlevomatCodingStandard.Operators.DisallowEqualOperators
        : ($value == $noneValue);

    /** @var Option<U> */
    return $same
        ? Option\none()
        : Option\some($value);
}

/**
 * Execute a callable and transform the result into an `Option`.
 * It will be a `Some` option containing the result if it is different from `$noneValue` (default `null`).
 *
 * # Examples
 *
 * Successful execution:
 *
 * ```
 * self::assertEq(Option\of(fn() => "fruits"), Option\some("fruits"));
 * ```
 *
 * Convertion of `null` to `Option\None`:
 *
 * ```
 * self::assertEq(Option\of(fn() => null), Option\none());
 * ```
 *
 * @template U
 * @param callable():U $callback
 * @return Option<U>
 */
function of(callable $callback, mixed $noneValue = null, bool $strict = true): Option
{
    return Option\fromValue($callback(), $noneValue, $strict);
}

/**
 * Execute a callable and transform the result into an `Option` as `Option\of()` does
 * but also return `Option\None` if it an exception matching $exceptionClass was thrown.
 *
 * # Examples
 *
 * Successful execution:
 *
 * ```
 * self::assertEq(Option\tryOf(fn () => strtolower("FRUITS")), Option\some("fruits"));
 * ```
 *
 * Convertion of `null` to `Option\None`:
 *
 * ```
 * self::assertEq(Option\tryOf(fn() => null), Option\none());
 * ```
 *
 * Checked Exception:
 *
 * ```
 * self::assertEq(Option\tryOf(fn () => new \DateTimeImmutable("nope")), Option\none());
 * ```
 *
 * Unchecked Exception:
 *
 * ```
 * self::assertEq(Option\tryOf(fn () => 1 / 0), Option\none());
 * // @throws DivisionByZeroError Division by zero
 * ```
 *
 * @template U
 * @template E of \Throwable
 * @param callable():U $callback
 * @param class-string<E> $exceptionClass
 * @return Option<U>
 * @throws \Throwable
 */
function tryOf(
    callable $callback,
    mixed $noneValue = null,
    bool $strict = true,
    string $exceptionClass = \Exception::class,
): Option {
    try {
        return Option\of($callback, $noneValue, $strict);
    } catch (\Throwable $th) {
        if (\is_a($th, $exceptionClass)) {
            return Option\none();
        }

        throw $th;
    }
}

/**
 * Converts from `Option<Option<T>>` to `Option<T>`.
 *
 * # Examples
 *
 * ```
 * $x = Option\Some("vegetables");
 * self::assertSame(Option\flatten(Option\some($x)), $x);
 * self::assertSame(Option\flatten(Option\some(Option\none())), Option\none());
 * self::assertSame(Option\flatten(Option\none()), Option\none());
 * ```
 *
 * @template U
 * @param Option<Option<U>> $option
 * @return Option<U>
 * @psalm-suppress InvalidReturnType
 */
function flatten(Option $option): Option
{
    /** @psalm-suppress InvalidReturnStatement */
    return $option instanceof Option\None
        ? $option
        /** @phpstan-ignore missingType.checkedException */
        : $option->unwrap();
}

/**
 * Unzips an option containing a tuple of two options.
 *
 * If `self` is `Some([a, b])` this method returns `[Some(a), Some(b)]`. Otherwise, `[None, None]` is returned.
 *
 * ```
 * $x = Option\Some("vegetables");
 * self::assertEq(Option\unzip(Option\some(["a", 2])), [Option\some("a"), Option\some(2)]);
 * self::assertSame(Option\unzip(Option\none()), [Option\none(), Option\none()]);
 * ```
 *
 * @template U
 * @template V
 * @param Option<array{U, V}> $option
 * @return array{Option<U>, Option<V>}
 */
function unzip(Option $option): array
{
    /** @var array{Option<U>, Option<V>} */
    return $option->mapOrElse(
        static fn (array $a): array => [Option\some($a[0]), Option\some($a[1])],
        static fn (): array => [Option\none(), Option\none()],
    );
}

/**
 * Transposes an `Option` of a `Result` into a `Result` of an `Option`.
 *
 * `None` will be mapped to `Ok(None)`.
 * `Some(Ok(_))` and `Some(Err(_))` will be mapped to `Ok(Some(_))` and `Err(_)`.
 *
 * ```
 * use TH\Maybe\Result;
 *
 * self::assertEq(Result\ok(Option\some(4)), Option\transpose(Option\some(Result\ok(4))));
 * self::assertEq(Result\err("meat"), Option\transpose(Option\some(Result\err("meat"))));
 * self::assertEq(Option\transpose(Option\none()), Result\ok(Option\none()));
 * ```
 *
 * @template U
 * @template E
 * @param Option<Result<U, E>> $option
 * @return Result<Option<U>, E>
 */
#[ExamplesSetup(IgnoreUnusedResults::class)]
function transpose(Option $option): Result
{
    /** @var Result<Option<U>, E> */
    return $option->mapOrElse(
        // @phpstan-ignore-next-line
        static fn (Result $result) => $result->map(Option\some(...)),
        static fn () => Result\ok(Option\none()),
    );
}
