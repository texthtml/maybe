<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Option;
use TH\Maybe\Tests\Provider;

final class UnwrapTest extends TestCase
{
    use Provider\Values;

    public function testExpectNone(): void
    {
        $this->expectExceptionObject(new \RuntimeException("This should fail"));

        Option\none()->expect("This should fail");
    }

    /**
     */
     #[DataProvider('values')]
    public function testExpectSome(mixed $value): void
    {
        Assert::assertSame($value, Option\some($value)->expect("This should succeed"));
    }

    public function testUnwrapNone(): void
    {
        $this->expectExceptionObject(new \RuntimeException("Unwrapping a `None` value"));

        Option\none()->unwrap();
    }

    /**
     */
     #[DataProvider('values')]
    public function testUnwrapSome(mixed $value): void
    {
        Assert::assertSame($value, Option\some($value)->unwrap());
    }

    /**
     */
     #[DataProvider('values')]
    public function testUnwrapOrNone(mixed $value): void
    {
        Assert::assertSame($value, Option\none()->unwrapOr($value));
    }

    /**
     */
     #[DataProvider('values')]
    public function testUnwrapOrSome(mixed $value): void
    {
        Assert::assertSame($value, Option\some($value)->unwrapOr(false));
    }

    /**
     */
     #[DataProvider('values')]
    public function testUnwrapOrElseNone(mixed $value): void
    {
        Assert::assertSame($value, Option\none()->unwrapOrElse(static fn () => $value));
    }

    /**
     */
     #[DataProvider('values')]
    public function testUnwrapOrElseSome(mixed $value): void
    {
        Assert::assertSame($value, Option\some($value)->unwrapOrElse(static fn () => false));
    }
}
