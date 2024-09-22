<?php declare(strict_types=1);

// This file is analysed by Psalm and PHPStan they are both configured to
// complain if "suppress" annotations are useless so that we know that those
// invalid examples need their "ignore" annotations.
// Annotation with 🙈 are for Psalm / PHPStan false positives.
// The other annotations - with 🎯 - are expected issues that Psalm / PHPStan should detect.

namespace TH\Maybe\Tests\TypeHinting\option;

use TH\Maybe\Option;
use TH\Maybe\Tests\TypeHinting\option as here;

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
        /**
         * @psalm-suppress RedundantCondition 🙈
         * @psalm-suppress InvalidReturnStatement 🎯
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
        /** @psalm-suppress MissingThrowsDocblock,RedundantCondition 🙈 */
        // @phpstan-ignore-next-line 🙈 Function TH\Maybe\Tests\TypeHinting\test_is_some() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $option->unwrap();
    }

    /**
     * @psalm-suppress RedundantConditionGivenDocblockType 🙈
     * @psalm-suppress MissingThrowsDocblock 🎯
     */
    // @phpstan-ignore-next-line 🎯 Dead catch - RuntimeException is never thrown in the try block.
    return $option->unwrap();
}

/**
 * @param Option<int> $option
 */
function test_is_none(Option $option): int
{
    if ($option->isNone()) {
        /**
         * @psalm-suppress NoValue 🙈
         * @psalm-suppress MissingThrowsDocblock,TypeDoesNotContainType 🎯
         */
        // @phpstan-ignore-next-line 🎯 Function test_instanceof_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $option->unwrap();
    }

    /** @psalm-suppress MissingThrowsDocblock,RedundantCondition 🙈 */
    // @phpstan-ignore-next-line 🙈 Function TH\Maybe\Tests\TypeHinting\test_is_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
    return $option->unwrap();
}

/**
 * @param Option<int> $option
 */
function test_instanceof_some(Option $option): int
{
    if ($option instanceof Option\Some) {
        /** @psalm-suppress MissingThrowsDocblock,RedundantCondition 🙈 */
        // @phpstan-ignore-next-line 🙈 Function TH\Maybe\Tests\TypeHinting\test_instanceof_some() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $option->unwrap();
    }

    /** @psalm-suppress MissingThrowsDocblock 🎯 */
    // @phpstan-ignore-next-line 🎯 Dead catch - RuntimeException is never thrown in the try block.
    return $option->unwrap();
}

/**
 * @param Option<int> $option
 */
function test_instanceof_none(Option $option): int
{
    if ($option instanceof Option\None) {
        /** @psalm-suppress NoValue,MissingThrowsDocblock,TypeDoesNotContainType 🎯 */
        // @phpstan-ignore-next-line 🎯 Function test_instanceof_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
        return $option->unwrap();
    }

    /** @psalm-suppress MissingThrowsDocblock 🙈 */
    // @phpstan-ignore-next-line 🙈 Function TH\Maybe\Tests\TypeHinting\test_instanceof_none() throws checked exception RuntimeException but it's missing from the PHPDoc @throws tag.
    return $option->unwrap();
}

function test_call_a_function_with_none(): void
{
    here\test_is_none(Option\none());
}

function test_call_a_function_with_some(): void
{
    here\test_is_none(Option\some(1));

    /** @psalm-suppress InvalidArgument 🎯 */
    // @phpstan-ignore-next-line 🎯 Parameter #1 $option of function TH\Maybe\Tests\TypeHinting\option\test_is_none expects TH\Maybe\Option<int>, TH\Maybe\Option\Some<string> given.
    here\test_is_none(Option\some("1"));
}
