<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Option;
use TH\Maybe\Result;

/**
 * Return a `Err` result
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
 * Return a `Ok` Result containing `$value`
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
 * use TH\Maybe\Result;
 *
 * $x = Result\ok(3);
 * assert(Result\flatten(Result\ok($x)) === $x);
 *
 * $x = Result\err("deity");
 * assert(Result\flatten($y = Result\ok($x)) === $x);
 *
 * assert(Result\flatten($x) == Result\err("deity"));
 * ```
 *
 * @template U
 * @template F
 * @param Result<Result<U, F>, F> $result
 * @return Result<U, F>
 */
#[\TH\Maybe\Tests\Attributes\ExamplesSetup(\TH\Maybe\Tests\Helpers\IgnoreUnusedResults::class)]
function flatten(Result $result): Result
{
    return $result->unwrapOrElse(static fn (): Result => clone $result);
}

/**
 * Transposes a `Result` of an `Option` into an `Option` of a `Result`.
 *
 * `Ok(None)` will be mapped to `None`.
 * `Ok(Some(_))` and `Err(_)` will be mapped to `Some(Ok(_))` and `Some(Err(_))`.
 *
 * ```
 * use TH\Maybe\{Result, Option};
 *
 * assert(Result\transpose(Result\ok(Option\none())) === Option\none());
 *
 * $x = Result\ok(Option\some(4));
 * assert(Result\transpose($x) == Option\some(Result\ok(4)));
 *
 * $x = Result\err("meat");
 * assert(Result\transpose($x) == Option\some($x));
 * ```
 *
 * @template U
 * @template F
 * @param Result<Option<U>, F> $result
 * @return Option<Result<U, F>>
 */
#[\TH\Maybe\Tests\Attributes\ExamplesSetup(\TH\Maybe\Tests\Helpers\IgnoreUnusedResults::class)]
function transpose(Result $result): Option
{
    return $result->mapOrElse(
        static fn (Option $option) => $option->map(Result\ok(...)),
        static fn () => Option\some(clone $result),
    );
}
