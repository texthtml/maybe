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
 * @template U
 * @template F
 * @param Result<Result<U, F>, F> $result
 * @return Result<U, F>
 */
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
 * @template U
 * @template F
 * @param Result<Option<U>, F> $result
 * @return Option<Result<U, F>>
 */
function transpose(Result $result): Option
{
    return $result->mapOrElse(
        static fn (Option $option) => $option->map(Result\ok(...)),
        static fn () => Option\some(clone $result),
    );
}
