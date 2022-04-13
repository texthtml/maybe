<?php declare(strict_types=1);

namespace TH\Maybe\Option;

use TH\Maybe\Option;
use TH\Maybe\Result;

/**
 * @template T
 * @param T $value
 * @return Option\Some<T>
 */
function some(mixed $value): Option\Some
{
    return new Option\Some($value);
}

/**
 * @return Option\None<mixed>
 */
function none(): Option\None
{
    return Option\None::instance;
}

/**
 * Transform a value into an Option.
 * It will be a Some option containing $value if $value is different from $noneValue (default `null`)
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
 * Converts from `Option<Option<T>>` to `Option<T>`.
 *
 * @template U
 * @param Option<Option<U>> $option
 * @return Option<U>
 */
function flatten(Option $option): Option
{
    /** @var Option<U> */
    return $option instanceof Option\None
        ? Option\none()
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
function unzip(Option $option): array
{
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
 * @template U
 * @template E
 * @param Option<Result<U, E>> $option
 * @return Result<Option<U>, E>
 */
function transpose(Option $option): Result
{
    return $option->mapOrElse(
        static fn (Result $result) => $result->map(Option\some(...)),
        static fn () => Result\ok(Option\none()),
    );
}
