<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Result;

use PHPUnit\Framework\TestCase;
use TH\Maybe\Result;
use TH\Maybe\Tests\Assert;

final class CollectTest extends TestCase
{
    public function testCollectOfOkResults(): void
    {
        /** @var list<Result<int, string>> $results */
        $results = [Result\ok(1), Result\ok(2), Result\ok(3)];

        $collected = Result\collect($results);

        Assert::assertEquals($expected = Result\ok([1, 2, 3]), $collected);

        Assert::assertResultNotUsed($collected);
        Assert::assertResultNotUsed($expected);
    }

    public function testCollectOfEmptyIterable(): void
    {
        /** @var list<Result<int, string>> $empty */
        $empty = [];

        $collected = Result\collect($empty);

        Assert::assertEquals($expected = Result\ok([]), $collected);

        Assert::assertResultNotUsed($collected);
        Assert::assertResultNotUsed($expected);
    }

    public function testCollectOfEmptyGenerator(): void
    {
        $empty = (static function (): iterable {
            yield from [];
        })();

        /** @var iterable<Result<int, string>> $empty */
        $empty = $empty;

        $collected = Result\collect($empty);

        Assert::assertEquals($expected = Result\ok([]), $collected);

        Assert::assertResultNotUsed($collected);
        Assert::assertResultNotUsed($expected);
    }

    public function testCollectShortCircuitsOnFirstErr(): void
    {
        $first = Result\ok(1);
        $second = Result\err('nope');
        $third = Result\ok(3);

        /** @var list<Result<int, string>> $results */
        $results = [$first, $second, $third];

        $collected = Result\collect($results);

        Assert::assertEquals($expected = Result\err('nope'), $collected);

        Assert::assertResultUsed($first);
        Assert::assertResultUsed($second);
        Assert::assertResultNotUsed($third);
        Assert::assertResultNotUsed($collected);
        Assert::assertResultNotUsed($expected);
    }

    public function testCollectStopsIterationAfterErr(): void
    {
        $visited = 0;

        /**
         * @var iterable<Result<int, string>> $results
         */
        $results = (static function () use (&$visited): \Generator {
            yield Result\ok(1);
            $visited++;
            yield Result\err('nope');
            $visited++;
            yield Result\ok(3);
        })();

        $collected = Result\collect($results);

        Assert::assertEquals($expected = Result\err('nope'), $collected);
        Assert::assertSame(1, $visited);

        Assert::assertResultNotUsed($collected);
        Assert::assertResultNotUsed($expected);
    }

    public function testCollectReturnsAFreshResult(): void
    {
        $err = Result\err('nope');

        /** @var list<Result<int, string>> $results */
        $results = [$err];

        $collected = Result\collect($results);

        Assert::assertResultUsed($err);
        Assert::assertResultNotUsed($collected);
    }
}
