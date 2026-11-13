<?php declare(strict_types=1);

// This file is analysed by Psalm, PHPStan and Mago they are all configured to
// complain if "suppress" annotations are useless so that we know that those
// invalid examples need their "ignore" annotations.
// Annotation with 🙈 are for Psalm / PHPStan / Mago false positives.
// Annotation with 🫥 marks a tool's blind spot: it fails to report an issue on this line that other tools detect.
// The other annotations - with 🎯 - are expected issues that Psalm / PHPStan / Mago should detect.

namespace TH\Maybe\Tests\TypeHinting\option;

use TH\Maybe\Option;
use TH\Maybe\Tests\TypeHinting\option as here;

// 🫥 phpstan: blind spot — no missing-return check; psalm & mago both flag it (psalm as "not all code paths end in a return")
// @mago-expect analysis:missing-return-statement 🎯
/**
 * @param Option<string> $option
 * @throws \RuntimeException
 * @psalm-suppress InvalidReturnType
 */
function test_generic_type(Option $option): int
{
    /** @psalm-suppress RedundantConditionGivenDocblockType 🎯 */
    // @mago-expect analysis:redundant-condition 🎯
    // @phpstan-ignore-next-line 🎯 Call to function is_string() with string will always evaluate to true.
    if (\is_string($option->unwrap())) {
        // @mago-expect analysis:invalid-return-statement 🎯
        /**
         * @psalm-suppress InvalidReturnStatement,RedundantCondition 🎯
         */
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
        // 🫥 phpstan & mago: blind spot — they miss the RedundantCondition psalm catches (the unwrap() assert repeats the already-known Some type)
        /**
         * @psalm-suppress MissingThrowsDocblock 🙈 https://github.com/vimeo/psalm/issues/2912
         * @psalm-suppress RedundantCondition 🎯
         */
        return $option->unwrap();
    }

    // @mago-expect analysis:impossible-type-comparison 🎯
    // @mago-expect analysis:never-return 🎯
    /** @psalm-suppress MissingThrowsDocblock 🎯 */
    // @phpstan-ignore-next-line 🎯 Function test_is_some() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
    return $option->unwrap();
}

/**
 * @param Option<int> $option
 */
function test_is_none(Option $option): int
{
    if ($option->isNone()) {
        // 🫥 phpstan: blind spot — misses that this return is dead code (psalm NoValue & mago never-return both flag it)
        // @mago-expect analysis:impossible-type-comparison 🎯
        // @mago-expect analysis:never-return 🎯
        /** @psalm-suppress NoValue,TypeDoesNotContainType,MissingThrowsDocblock 🎯 */
        // @phpstan-ignore-next-line 🎯 Function test_is_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $option->unwrap();
    }

    /**
     * 🫥 phpstan & mago: blind spot — they miss the redundant assertion psalm catches (unwrap()'s assert repeats the narrowed Some type)
     *
     * @psalm-suppress MissingThrowsDocblock 🙈
     * @psalm-suppress RedundantConditionGivenDocblockType 🎯
     */
    return $option->unwrap();
}

/**
 * @param Option<int> $option
 */
function test_instanceof_some(Option $option): int
{
    if ($option instanceof Option\Some) {
        // 🫥 phpstan & mago: blind spot — they miss the RedundantCondition psalm catches (Some<int>&Option<int> is always Some<int>)
        // @mago-ignore analysis:mixed-return-statement 🙈 mago cannot bind Some's template T through the Option<int>&Some intersection (psalm resolves int via unwrap()'s @return T)
        /**
         * @psalm-suppress MissingThrowsDocblock 🙈 https://github.com/vimeo/psalm/issues/2912
         * @psalm-suppress RedundantCondition 🎯
         */
        // @phpstan-ignore-next-line 🙈 phpstan cannot bind Some's template T through the Option<int>&Some intersection, so it infers mixed (psalm resolves int via unwrap()'s @return T)
        return $option->unwrap();
    }

    // 🫥 mago: misses that this return is dead code ($option narrowed to None ⇒ unwrap() throws) — psalm/phpstan both flag this line
    /** @psalm-suppress MissingThrowsDocblock 🎯 */
    // @phpstan-ignore-next-line 🎯 Function test_instanceof_some() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
    return $option->unwrap();
}

/**
 * @param Option<int> $option
 */
function test_instanceof_none(Option $option): int
{
    if ($option instanceof Option\None) {
        // @mago-expect analysis:impossible-type-comparison 🎯
        // @mago-expect analysis:never-return 🎯
        /** @psalm-suppress NoValue,MissingThrowsDocblock,TypeDoesNotContainType 🎯 */
        // @phpstan-ignore-next-line 🎯 Function test_instanceof_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $option->unwrap();
    }

    /** @psalm-suppress MissingThrowsDocblock 🙈 */
    // @phpstan-ignore-next-line 🙈 Function test_instanceof_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
    return $option->unwrap();
}

function test_call_a_function_with_none(): void
{
    here\test_is_none(Option\none());
}

function test_call_a_function_with_some(): void
{
    // @mago-ignore analysis:possibly-invalid-argument 🙈 mago does not widen literal Some<int(1)> to Option<int>
    here\test_is_none(Option\some(1));

    // @mago-expect analysis:possibly-invalid-argument 🎯
    /** @psalm-suppress InvalidArgument 🎯 */
    // @phpstan-ignore-next-line 🎯 Parameter #1 $option of function TH\Maybe\Tests\TypeHinting\option\test_is_none expects TH\Maybe\Option<int>, TH\Maybe\Option\Some<string> given.
    here\test_is_none(Option\some('1'));
}
