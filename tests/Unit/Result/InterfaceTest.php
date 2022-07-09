<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;

final class InterfaceTest extends TestCase
{
    public function testInstanceOfResult(): void
    {
        Assert::assertInstanceOf(Result::class, $result = Result\err(null));
        Assert::assertResultNotUsed($result);

        Assert::assertInstanceOf(Result::class, $result = Result\ok(null));
        Assert::assertResultNotUsed($result);
    }

    public function testInstanceOfErr(): void
    {
        Assert::assertInstanceOf(Result\Err::class, $result = Result\err(null));
        Assert::assertResultNotUsed($result);

        Assert::assertNotInstanceOf(Result\Err::class, $result = Result\ok(null));
        Assert::assertResultNotUsed($result);
    }

    public function testInstanceOfOk(): void
    {
        Assert::assertNotInstanceOf(Result\Ok::class, $result = Result\err(null));
        Assert::assertResultNotUsed($result);

        Assert::assertInstanceOf(Result\Ok::class, $result = Result\ok(null));
        Assert::assertResultNotUsed($result);
    }
}
