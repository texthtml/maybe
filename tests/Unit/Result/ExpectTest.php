<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;

final class ExpectTest extends TestCase
{
    public function testExpectErr(): void
    {
        $this->expectExceptionObject(new \RuntimeException('This should fail'));

        Result\err(null)->expect('This should fail');
    }

    public function testExpectErrWithAThrowableErrValue(): void
    {
        $this->expectExceptionObject(new \RuntimeException('This should fail'));

        Result\err(new \LogicException('Something went wrong'))->expect('This should fail');
    }

    public function testExpectOk(): void
    {
        Assert::assertSame('value', Result\ok('value')->expect('This should succeed'));
    }
}
