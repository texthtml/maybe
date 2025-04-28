<?php declare(strict_types=1);

namespace TH\Maybe\Internal;

use function TH\Maybe\Option\isOfAnyClass;

/**
 * Call $callback with $exception if it matches one of $exceptionClasses
 * and return its value, or rethrow it otherwise.
 *
 * @template E of \Throwable
 * @template T
 * @param E $error
 * @param callable(E): T $callback
 * @param class-string<E> ...$exceptionClasses
 * @return T
 * @throws \Throwable
 * @internal
 * @nodoc
 */
function trap(
    \Throwable $error,
    callable $callback,
    string ...$exceptionClasses,
): mixed {
    if (isOfAnyClass($error, $exceptionClasses)) {
        return $callback($error);
    }

    throw $error;
}
