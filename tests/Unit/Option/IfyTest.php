<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Tests\Provider;

final class IfyTest extends TestCase
{
    use Provider\Options;

    /**
     * @dataProvider fromValueMatrix
     * @param Option<mixed> $expected
     */
    public function testIfy(Option $expected, mixed $value, mixed $noneValue, bool $strict = true): void
    {
        Assert::assertEquals($expected, Option\ify(static fn () => $value, $noneValue, strict: $strict)());
    }

    /**
     * @dataProvider fromValueMatrix
     * @param Option<mixed> $expected
     */
    public function testTryOf(Option $expected, mixed $value, mixed $noneValue, bool $strict = true): void
    {
        Assert::assertEquals($expected, Option\tryIfy(static fn () => $value, $noneValue, strict: $strict)());
    }

    public function testOfDefaultToNull(): void
    {
        Assert::assertEquals(Option\none(), Option\ify(static fn () => null)());
        Assert::assertEquals(Option\some(1), Option\ify(static fn () => 1)());
    }

    public function testTryOfDefaultToNull(): void
    {
        Assert::assertEquals(Option\none(), Option\tryIfy(static fn () => null)());
        Assert::assertEquals(Option\some(1), Option\tryIfy(static fn () => 1)());
    }

    public function testOfDefaultToStrict(): void
    {
        $o = (object)[];

        Assert::assertEquals(Option\none(), Option\ify(static fn () => $o, (object)[], strict: false)());
        Assert::assertEquals($o, Option\ify(static fn () => $o, (object)[])()->unwrap());
    }

    public function testTryOfDefaultToStrict(): void
    {
        $o = (object)[];

        Assert::assertEquals(Option\none(), Option\tryIfy(static fn () => $o, (object)[], strict: false)());
        Assert::assertEquals($o, Option\tryIfy(static fn () => $o, (object)[])()->unwrap());
    }

    public function testTryOfExeptions(): void
    {
        // @phpstan-ignore-next-line
        Assert::assertEquals(Option\none(), Option\tryIfy(static fn () => new \DateTimeImmutable("nope"))());

        try {
            // @phpstan-ignore-next-line
            Option\tryIfy(static fn () => 1 / 0)();
            Assert::fail("An exception should have been thrown");
        } catch (\DivisionByZeroError) {
        }
    }
}
