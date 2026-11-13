<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Provider;

final class UnwrapTest extends TestCase
{
    use Provider\Values;

    public function testUnwrapErr(): void
    {
        $this->expectExceptionObject(new \RuntimeException('Unwrapping `Err`: N;'));

        Result\err(null)->unwrap();
    }

    public function testUnwrapErrException(): void
    {
        $ex = new \LogicException('Something went wrong');

        $this->expectExceptionObject($ex);

        Result\err($ex)->unwrap();
    }

    public function testUnwrapErrOnOk(): void
    {
        $this->expectExceptionObject(new \RuntimeException('Unwrapping err on `Ok`: i:2;'));

        Result\ok(2)->unwrapErr();
    }

    #[DataProvider('values')]
    public function testUnwrapOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->unwrap());
    }

    #[DataProvider('values')]
    public function testUnwrapOrErr(mixed $value): void
    {
        Assert::assertSame($value, Result\err(null)->unwrapOr($value));
    }

    #[DataProvider('values')]
    public function testUnwrapOrOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->unwrapOr(false));
    }

    #[DataProvider('values')]
    public function testUnwrapOrElseErr(mixed $value): void
    {
        Assert::assertSame($value, Result\err(null)->unwrapOrElse(static fn () => $value));
    }

    #[DataProvider('values')]
    public function testUnwrapOrElseOk(mixed $value): void
    {
        Assert::assertSame($value, Result\ok($value)->unwrapOrElse(static fn () => false));
    }
}
