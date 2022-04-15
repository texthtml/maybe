<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TH\Maybe\Option;

final class FilterTest extends TestCase
{
    /**
     * @dataProvider filterMatrix
     * @template T
     * @param Option<T> $option
     * @param array<T> $expectedCalls
     */
    public function testFilter(Option $option, bool $filterResult, bool $expectNone, array $expectedCalls): void
    {
        $calls = [];

        Assert::assertEquals(
            $expectNone ? Option\none() : $option,
            $option->filter(static function(mixed $value) use ($filterResult, &$calls): bool {
                $calls[] = $value;

                return $filterResult;
            }),
        );

        Assert::assertSame($expectedCalls, $calls);
    }

    /**
     * @return iterable<array{
     *   Option<mixed>,
     *   bool,
     *   bool,
     *   array<mixed>
     * }>
     */
    public function filterMatrix(): iterable
    {
        yield "none-true" => [
            Option\none(),
            true,
            true,
            [],
        ];

        yield "none-false" => [
            Option\none(),
            false,
            true,
            [],
        ];

        yield "some-true" => [
            Option\some(5),
            true,
            false,
            [5],
        ];

        yield "some-false" => [
            Option\some(42),
            false,
            true,
            [42],
        ];
    }
}
