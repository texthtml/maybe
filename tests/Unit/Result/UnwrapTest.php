<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Provider;

final class UnwrapTest extends TestCase
{
    use Provider\Values;

    public function testExpectErr(): void
    {
        $this->expectExceptionObject(new \RuntimeException("This should fail"));

        Result\err(null)->expect("This should fail");
    }

    /**
     * @dataProvider values
     */
    public function testExpectOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->expect("This should succeed"));
    }

    public function testUnwrapErr(): void
    {
        $this->expectExceptionObject(new \RuntimeException("Unwrapping `Err`: N;"));

        Result\err(null)->unwrap();
    }

    public function testUnwrapErrException(): void
    {
        $ex = new \LogicException("Something went wrong");

        $this->expectExceptionObject($ex);

        Result\err($ex)->unwrap();
    }

    /**
     * @dataProvider values
     */
    public function testUnwrapOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->unwrap());
    }

    /**
     * @dataProvider values
     */
    public function testUnwrapOrErr(mixed $value): void
    {
        Assert::assertSame($value, Result\err(null)->unwrapOr($value));
    }

    /**
     * @dataProvider values
     */
    public function testUnwrapOrOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->unwrapOr(false));
    }

    /**
     * @dataProvider values
     */
    public function testUnwrapOrElseErr(mixed $value): void
    {
        Assert::assertSame($value, Result\err(null)->unwrapOrElse(static fn () => $value));
    }

    /**
     * @dataProvider values
     */
    public function testUnwrapOrElseOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->unwrapOrElse(static fn () => false));
    }
}
