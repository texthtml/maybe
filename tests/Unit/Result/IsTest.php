<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;
use TH\Maybe\Tests\Provider;

final class IsTest extends TestCase
{
    use Provider\Values;

    /**
     * @dataProvider values
     */
    public function testIsOk(mixed $value): void
    {
        $result = Result\ok($value);

        Assert::assertTrue($result->isOk());
        Assert::assertResultUsed($result);

        $result = Result\err($value);

        Assert::assertFalse($result->isOk());
        Assert::assertResultUsed($result);
    }

    /**
     * @dataProvider values
     */
    public function testIsErr(mixed $value): void
    {
        $result = Result\ok($value);

        Assert::assertFalse($result->isErr());
        Assert::assertResultUsed($result);

        $result = Result\err($value);

        Assert::assertTrue($result->isErr());
        Assert::assertResultUsed($result);
    }

    /**
     * @dataProvider values
     */
    public function testIsOkAnd(mixed $value): void
    {
        $result = Result\ok($value);

        Assert::assertTrue($result->isOkAnd(static fn (mixed $v) => $v === $value));
        Assert::assertFalse($result->isOkAnd(static fn (mixed $v) => $v !== $value));
        Assert::assertResultUsed($result);

        $result = Result\err($value);

        Assert::assertFalse($result->isOkAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
        Assert::assertFalse($result->isOkAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
        Assert::assertResultUsed($result);
    }

    /**
     * @dataProvider values
     */
    public function testIsErrAnd(mixed $value): void
    {
        $result = Result\ok($value);

        Assert::assertFalse($result->isErrAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
        Assert::assertFalse($result->isErrAnd(static fn (mixed $v) => Assert::fail('predicate should be called')));
        Assert::assertResultUsed($result);

        $result = Result\err($value);

        Assert::assertTrue($result->isErrAnd(static fn (mixed $v) => $v === $value));
        Assert::assertFalse($result->isErrAnd(static fn (mixed $v) => $v !== $value));
        Assert::assertResultUsed($result);
    }
}
