<?php declare(strict_types=1);

namespace TH\Maybe\Internal;

/**
 * Call $callback with $exception if it matches one of $exceptionClasses
 * and return its value, or rethrow it otherwise.
 *
 * @template E of \Throwable
 * @template T
 * @param E $error
 * @param callable(E): T $callback
 * @param class-string<E> $exceptionClasses
 * @throws \Throwable
 * @return T
 * @internal
 * @nodoc
 */
function trap(
    \Throwable $error,
    callable $callback,
    string ...$exceptionClasses,
): mixed {
    foreach ($exceptionClasses as $exceptionClass) {
        if (\is_a($error, $exceptionClass)) {
            return $callback($error);
        }
    }

    throw $error;
}
