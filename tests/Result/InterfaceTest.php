<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;

class InterfaceTest extends TestCase
{
    public function testInstanceOfResult(): void
    {
        Assert::assertInstanceOf(Result::class, Result\err(null));
        Assert::assertInstanceOf(Result::class, Result\ok(null));
    }

    public function testInstanceOfErr(): void
    {
        Assert::assertInstanceOf(Result\Err::class, Result\err(null));
        Assert::assertNotInstanceOf(Result\Err::class, Result\ok(null));
    }

    public function testInstanceOfOk(): void
    {
        Assert::assertNotInstanceOf(Result\Ok::class, Result\err(null));
        Assert::assertInstanceOf(Result\Ok::class, Result\ok(null));
    }
}
