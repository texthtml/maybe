<?php declare(strict_types=1);

namespace TH\Maybe\Internal;

/**
 * Check if the type of the given value is any of the passed classes.
 *
 * @template T
 * @param iterable<class-string<T>> $classes
 * @psalm-assert-if-false !T $value
 * @psalm-assert-if-true T $value
 */
function isOfAnyClass(object $value, iterable $classes): bool
{
    foreach ($classes as $class) {
        // @mago-ignore analysis:less-specific-nested-argument-type -- mago widens class-string<T> to class-string<m>
        if (\is_a($value, $class)) {
            return true;
        }
    }

    return false;
}
