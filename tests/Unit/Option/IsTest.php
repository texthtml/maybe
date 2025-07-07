<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Option;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class IsTest extends TestCase
{
    use Provider\Values;

    /**
     */
     #[DataProvider('values')]
    public function testIsSome(mixed $value): void
    {
        $option = Option\some($value);

        Assert::assertTrue($option->isSome());

        $option = Option\none();

        Assert::assertFalse($option->isSome());
    }

    /**
     */
     #[DataProvider('values')]
    public function testIsNone(mixed $value): void
    {
        $option = Option\some($value);

        Assert::assertFalse($option->isNone());

        $option = Option\none();

        Assert::assertTrue($option->isNone());
    }

    /**
     */
     #[DataProvider('values')]
    public function testIsSomeAnd(mixed $value): void
    {
        $option = Option\some($value);

        Assert::assertTrue($option->isSomeAnd(static fn (mixed $v) => $v === $value));
        Assert::assertFalse($option->isSomeAnd(static fn (mixed $v) => $v !== $value));

        $option = Option\none();

        Assert::assertFalse($option->isSomeAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
        Assert::assertFalse($option->isSomeAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
    }
}
