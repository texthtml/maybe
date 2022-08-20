<?php declare(strict_types=1);

// This file is analysed by Psalm and PHPStan they are both configured to
// complain if "suppress" annotations are useless so that we know that those
// invalid examples need their "ignore" annotations.

namespace TH\Maybe\Tests\TypeHinting;

use TH\Maybe\Option;

/**
 * @param Option<string> $option
 * @throws \RuntimeException
 * @psalm-suppress InvalidReturnType
 */
function test_generic_type(Option $option): int
{
    /** @psalm-suppress RedundantConditionGivenDocblockType */
    // @phpstan-ignore-next-line Call to function is_string() with string will always evaluate to true.
    if (\is_string($option->unwrap())) {
        /** @psalm-suppress InvalidReturnStatement */
        // @phpstan-ignore-next-line Function test_generic_type() should return int but returns string.
        return $option->unwrap();
    }
}

/**
 * @param Option<int> $option
 */
function test_instanceof_some(Option $option): int
{
    try {
        if ($option instanceof Option\Some) {
            /**
             * @psalm-suppress MissingThrowsDocblock
             */
            // @see https://github.com/phpstan/phpstan/issues/7609
            // @phpstan-ignore-next-line Function test_instanceof_some() should return int but returns mixed.
            return $option->unwrap();
        }

        return 3;
        // @phpstan-ignore-next-line Dead catch - RuntimeException is never thrown in the try block.
    } catch (\RuntimeException) {
        return 5;
    }
}

/**
 * @param Option<int> $option
 */
function test_instanceof_none(Option $option): int
{
    if ($option instanceof Option\None) {
        /** @psalm-suppress NoValue,MissingThrowsDocblock */
        // @phpstan-ignore-next-line Function test_instanceof_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $option->unwrap();
    }

    // Other classes could implement `Option`, so Psalm & PHPStan can't assume that `$option` is `Option\Some` here
    // Resulting in those unwanted detected issues.
    // The only way to prevent that is to use `instanceof Option\Some` instead

    /** @psalm-suppress MissingThrowsDocblock */
    // @phpstan-ignore-next-line Function test_instanceof_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
    return $option->unwrap();
}
