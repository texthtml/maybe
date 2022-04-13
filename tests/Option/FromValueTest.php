<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Tests\Provider;

class FromValueTest extends TestCase
{
    use Provider\Values;

    /**
     * @dataProvider fromValueMatrix
     * @param Option<mixed> $expected
     */
    public function testFromValue(Option $expected, mixed $value, mixed $noneValue, bool $strict = true): void
    {
        Assert::assertEquals($expected, Option\fromValue($value, $noneValue, strict: $strict));
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, mixed, 3?:bool}>
     */
    public function fromValueMatrix(): iterable
    {
        $o = (object)[];

        yield [Option\none(),     null, null];
        yield [Option\some(null), null, 0];

        yield [Option\none(),  0, 0];
        yield [Option\some(0), 0, 1];
        yield [Option\none(),  1, 1];
        yield [Option\some(1), 1, 0];
        yield [Option\some(1), 1, '1'];
        yield [Option\none(), 1, '1', false];
        yield [Option\none(), 1, true, false];
        yield [Option\some(1), 1, true];

        yield [Option\none(),    [], []];
        yield [Option\some([1]), [1], [2]];

        yield [Option\none(),   $o, $o];
        yield [Option\some($o), $o, (object)[]];
        yield [Option\none(),   $o, (object)[], false];
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
