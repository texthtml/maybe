<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Tests\Provider;

final class FromValueTest extends TestCase
{
    use Provider\Options;

    /**
     * @param Option<mixed> $expected
    */
    #[DataProvider('fromValueMatrix')]
    public function testFromValue(Option $expected, mixed $value, mixed $noneValue, bool $strict = true): void
    {
        Assert::assertEquals($expected, Option\fromValue($value, $noneValue, strict: $strict));
    }

    public function testFromValueDefaultToNull(): void
    {
        Assert::assertEquals(Option\none(), Option\fromValue(null));
        Assert::assertEquals(Option\some(1), Option\fromValue(1));
    }

    public function testFromValueDefaultToStrict(): void
    {
        $o = (object)[];

        Assert::assertEquals(Option\none(), Option\fromValue($o, (object)[], strict: false));
        Assert::assertEquals($o, Option\fromValue($o, (object)[])->unwrap());
    }
}
