<?php declare(strict_types=1);

// This file is analysed by Psalm, PHPStan and Mago they are all configured to
// complain if "suppress" annotations are useless so that we know that those
// invalid examples need their "ignore" annotations.
// Annotation with 🙈 are for Psalm / PHPStan / Mago false positives.
// Annotation with 🫥 marks a tool's blind spot: it fails to report an issue on this line that other tools detect.
// The other annotations - with 🎯 - are expected issues that Psalm / PHPStan / Mago should detect.

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
    // @mago-expect analysis:redundant-condition 🎯
    // @phpstan-ignore-next-line 🎯 Call to function is_string() with string will always evaluate to true.
    if (\is_string($result->unwrap())) {
        // @mago-expect analysis:invalid-return-statement 🎯
        /** @psalm-suppress InvalidReturnStatement 🎯 */
        // @phpstan-ignore-next-line 🎯 Function test_generic_type() should return int but returns string.
        return $result->unwrap();
    }

    /** @psalm-suppress NoValue 🎯 This function or method call never returns output */
    // 🫥 mago: blind spot — mago's dead-code analysis is purely control-flow based (the implicit else falls through), so it cannot see
    // that unwrapErr() returns never here; psalm & phpstan flag it via type narrowing
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
        return $result->unwrap();
    }

    /**
     * 🫥 phpstan & mago: blind spot — neither detects the undocumented throw that psalm catches (unwrap() on a narrowed Err always throws)
     *
     * @psalm-suppress MissingThrowsDocblock 🎯 Throwable is thrown but not caught - please either catch or add a @throws annotation */
    return $result->unwrap();
}

/**
 * @param Result<int,string> $result
 */
function test_is_none(Result $result): string
{
    if ($result->isErr()) {
        // @mago-ignore analysis:invalid-return-statement 🙈 mago does not narrow via isErr(), sees unwrapErr() as int|string
        /** @psalm-suppress MissingThrowsDocblock 🙈 Throwable is thrown but not caught - please either catch or add a @throws annotation */
        return $result->unwrapErr();
    }

    /** @psalm-suppress MissingThrowsDocblock 🙈 Throwable is thrown but not caught - please either catch or add a @throws annotation */
    return (string) $result->unwrap();
}
