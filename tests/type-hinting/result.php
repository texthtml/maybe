<?php declare(strict_types=1);

// This file is analysed by Psalm and PHPStan they are both configured to
// complain if "suppress" annotations are useless so that we know that those
// invalid examples need their "ignore" annotations.
// Annotation with 🙈 are for Psalm / PHPStan false positives.
// The other annotations - with 🎯 - are expected issues that Psalm / PHPStan should detect.

namespace TH\Maybe\Tests\TypeHinting\result;

use TH\Maybe\Result;

/**
 * @param Result<string,int> $result
 * @throws \Throwable
 * @psalm-suppress InvalidReturnType
 */
function test_generic_type(Result $result): int
{
    /** @psalm-suppress RedundantConditionGivenDocblockType 🎯 Docblock type string always contains string */
    // @phpstan-ignore-next-line 🎯 Call to function is_string() with string will always evaluate to true.
    if (\is_string($result->unwrap())) {
        /** @psalm-suppress InvalidReturnStatement 🎯 */
        // @phpstan-ignore-next-line 🎯 Function test_generic_type() should return int but returns string.
        return $result->unwrap();
    }

    /** @psalm-suppress TypeDoesNotContainType,NoValue 🎯 This function or method call never returns output */
    // @phpstan-ignore-next-line 🎯 Unreachable statement - code above always terminates.
    return $result->unwrapErr();
}

/**
 * @param Result<int,string> $result
 */
function test_is_ok(Result $result): int
{
    if ($result->isOk()) {
        /** @psalm-suppress MissingThrowsDocblock 🙈 Throwable is thrown but not caught - please either catch or add a @throws annotation */
        // @phpstan-ignore-next-line 🙈 Function TH\Maybe\Tests\TypeHinting\result\test_is_ok() should return int but returns mixed.
        return $result->unwrap();
    }

    /** @psalm-suppress MissingThrowsDocblock 🎯 Throwable is thrown but not caught - please either catch or add a @throws annotation */
    return $result->unwrap();
}

/**
 * @param Result<int,string> $result
 */
function test_is_none(Result $result): string
{
    if ($result->isErr()) {
        /** @psalm-suppress MissingThrowsDocblock 🙈 Throwable is thrown but not caught - please either catch or add a @throws annotation */
        // @phpstan-ignore-next-line 🙈 Function TH\Maybe\Tests\TypeHinting\test_is_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $result->unwrapErr();
    }

    /** @psalm-suppress MissingThrowsDocblock 🙈 Throwable is thrown but not caught - please either catch or add a @throws annotation */
    // @phpstan-ignore-next-line 🙈 Cannot cast mixed to string.
    return (string) $result->unwrap();
}
