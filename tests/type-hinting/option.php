<?php declare(strict_types=1);

// This file is analysed by Psalm and PHPStan they are both configured to
// complain if "suppress" annotations are useless so that we know that those
// invalid examples need their "ignore" annotations.
// Annotation with 🙈 are for Psalm / PHPStan false positives.
// The other annotations - with 🎯 - are expected issues that Psalm / PHPStan should detect.

namespace TH\Maybe\Tests\TypeHinting;

use TH\Maybe\Option;

/**
 * @param Option<string> $option
 * @throws \RuntimeException
 * @psalm-suppress InvalidReturnType
 */
function test_generic_type(Option $option): int
{
    /** @psalm-suppress RedundantConditionGivenDocblockType 🎯 */
    // @phpstan-ignore-next-line 🎯 Call to function is_string() with string will always evaluate to true.
    if (\is_string($option->unwrap())) {
        /** @psalm-suppress InvalidReturnStatement 🎯 */
        // @phpstan-ignore-next-line 🎯 Function test_generic_type() should return int but returns string.
        return $option->unwrap();
    }
}

/**
 * @param Option<int> $option
 */
function test_is_some(Option $option): int
{
    if ($option->isSome()) {
        /** @psalm-suppress MissingThrowsDocblock 🙈 */
        // @phpstan-ignore-next-line 🙈 Function TH\Maybe\Tests\TypeHinting\test_is_some() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $option->unwrap();
    }

    /** @psalm-suppress MissingThrowsDocblock 🎯 */
    // @phpstan-ignore-next-line 🎯 Dead catch - RuntimeException is never thrown in the try block.
    return $option->unwrap();
}

/**
 * @param Option<int> $option
 */
function test_is_none(Option $option): int
{
    if ($option->isNone()) {
        /** @psalm-suppress NoValue,MissingThrowsDocblock 🎯 */
        // @phpstan-ignore-next-line 🎯 Function test_instanceof_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $option->unwrap();
    }

    /** @psalm-suppress MissingThrowsDocblock 🙈 */
    // @phpstan-ignore-next-line 🙈 Function TH\Maybe\Tests\TypeHinting\test_is_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
    return $option->unwrap();
}
