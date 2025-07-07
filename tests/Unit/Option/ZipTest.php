<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Unit\Option;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TH\Maybe\Option;

final class ZipTest extends TestCase
{
    /**
     * @template L
     * @template R
     * @param Option<L> $left
     * @param Option<R> $right
     * @param Option<array{L, R}> $expected
     */
     #[DataProvider('zipMatrix')]
    public function testZip(Option $left, Option $right, Option $expected): void
    {
        Assert::assertEquals($expected, $left->zip($right));
    }

    /**
     * @return iterable<array{
     *   Option<mixed>,
     *   Option<mixed>,
     *   Option<array{mixed, mixed}>,
     * }>
     */
    public static function zipMatrix(): iterable
    {
        yield "none-none" => [
            Option\none(),
            Option\none(),
            Option\none(),
        ];

        yield "none-some" => [
            Option\none(),
            Option\some(42),
            Option\none(),
        ];

        yield "some-none" => [
            Option\some(42),
            Option\none(),
            Option\none(),
        ];

        yield "some-some" => [
            Option\some(42),
            Option\some("fishes"),
            Option\some([42, "fishes"]),
        ];
    }

    /**
     * @template L
     * @template R
     * @param Option<array{L, R}> $zipped
     * @param Option<L> $left
     * @param Option<R> $right
     */
     #[DataProvider('unzipMatrix')]
    public function testUnzip(Option $zipped, Option $left, Option $right): void
    {
        Assert::assertEquals([$left, $right], Option\unzip($zipped));
    }

    /**
     * @return iterable<array{
     *   Option<array{mixed, mixed}>,
     *   Option<mixed>,
     *   Option<mixed>,
     * }>
     */
    public static function unzipMatrix(): iterable
    {
        yield "none" => [
            Option\none(),
            Option\none(),
            Option\none(),
        ];

        yield "some" => [
            Option\some([42, "fishes"]),
            Option\some(42),
            Option\some("fishes"),
        ];
    }
}
