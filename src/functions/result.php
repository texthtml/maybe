<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\DocTest\Attributes\ExamplesSetup;
use TH\Maybe\Option;
use TH\Maybe\Result;
use TH\Maybe\Tests\Helpers\IgnoreUnusedResults;

/**
 * Return a `Result\Err` result.
 *
 * @template F
 * @param F $value
 * @return Result\Err<mixed, F>
 */
function err(mixed $value): Result\Err
{
    return new Result\Err($value);
}

/**
 * Return a `Result\Ok` Result containing `$value`.
 *
 * @template U
 * @param U $value
 * @return Result\Ok<U, mixed>
 */
function ok(mixed $value): Result\Ok
{
    return new Result\Ok($value);
}

/**
 * Converts from `Result<Result<T, E>, E>` to `Result<T, E>`.
 *
 * # Examples
 *
 * ```
 * $x = Result\ok(3);
 * self::assertSame(Result\flatten(Result\ok($x)), $x);
 *
 * $x = Result\err("deity");
 * self::assertSame(Result\flatten($y = Result\ok($x)), $x);
 *
 * self::assertEq(Result\flatten($x), Result\err("deity"));
 * ```
 *
 * @template U
 * @template F
 * @template R of Result<U, F>
 * @param Result<R, F> $result
 * @return R
 */
#[ExamplesSetup(IgnoreUnusedResults::class)]
function flatten(Result $result): Result
{
    return $result->unwrapOrElse(static fn () => clone $result); // @phpstan-ignore-line
}

/**
 * Transposes a `Result` of an `Option` into an `Option` of a `Result`.
 *
 * `Ok(None)` will be mapped to `None`.
 * `Ok(Some(_))` and `Err(_)` will be mapped to `Some(Ok(_))` and `Some(Err(_))`.
 *
 * ```
 * use TH\Maybe\Option;
 *
 * self::assertSame(Result\transpose(Result\ok(Option\none())), Option\none());
 *
 * $x = Result\ok(Option\some(4));
 * self::assertEq(Result\transpose($x), Option\some(Result\ok(4)));
 *
 * $x = Result\err("meat");
 * self::assertEq(Result\transpose($x), Option\some($x));
 * ```
 *
 * @template U
 * @template F
 * @param Result<Option<U>, F> $result
 * @return Option<Result<U, F>>
 */
#[ExamplesSetup(IgnoreUnusedResults::class)]
function transpose(Result $result): Option
{
    /** @var Option<Result<U, F>> */
    return $result->mapOrElse(
        // @phpstan-ignore-next-line
        static fn (Option $option) => $option->map(Result\ok(...)),
        static fn () => Option\some(clone $result),
    );
}
