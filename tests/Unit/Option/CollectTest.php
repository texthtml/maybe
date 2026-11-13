<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;
use TH\Maybe\Tests\Assert;

final class CollectTest extends TestCase
{
    public function testCollectOfSomeOptions(): void
    {
        /** @var list<Option<int>> $options */
        $options = [Option\some(1), Option\some(2), Option\some(3)];

        $collected = Option\collect($options);

        Assert::assertEquals(Option\some([1, 2, 3]), $collected);
    }

    public function testCollectOfEmptyIterable(): void
    {
        /** @var list<Option<int>> $empty */
        $empty = [];

        Assert::assertEquals(Option\some([]), Option\collect($empty));

        /** @var iterable<Option<int>> $generator */
        $generator = (static function (): iterable {
            yield from [];
        })();

        Assert::assertEquals(Option\some([]), Option\collect($generator));
    }

    public function testCollectShortCircuitsOnFirstNone(): void
    {
        /** @var list<Option<int>> $withNoneInMiddle */
        $withNoneInMiddle = [Option\some(1), Option\none(), Option\some(3)];

        Assert::assertEquals(Option\none(), Option\collect($withNoneInMiddle));

        /** @var list<Option<int>> $withNoneFirst */
        $withNoneFirst = [Option\none(), Option\some(2)];

        Assert::assertEquals(Option\none(), Option\collect($withNoneFirst));
    }

    public function testCollectPreservesOrder(): void
    {
        /** @var array{'a': Option<int>, 'b': Option<int>} $options */
        $options = ['a' => Option\some(1), 'b' => Option\some(2)];

        $collected = Option\collect($options);

        Assert::assertSame([1, 2], $collected->unwrap());
    }

    public function testCollectAcceptsIterables(): void
    {
        /** @var iterable<Option<int>> $options */
        $options = (static function (): iterable {
            yield Option\some(1);
        })();

        Assert::assertEquals(Option\some([1]), Option\collect($options));
    }
}
