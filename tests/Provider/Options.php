<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Provider;

use TH\Maybe\Option;

trait Options
{
    /**
     * @return iterable<string, array{
     *   left:Option<string>,
     *   right:Option<string>,
     *   expected:Option<string>
     * }>
     */
    public static function andMatrix(): iterable
    {
        /** @var Option<string> $none */
        $none = Option\none();
        $left = Option\some("left");
        $right = Option\some("right");

        yield "none-none" => [
            "left"     => $none,
            "right"    => $none,
            "expected" => $none,
        ];

        yield "none-some" => [
            "left"     => $none,
            "right"    => $right,
            "expected" => $none,
        ];

        yield "some-some" => [
            "left"     => $left,
            "right"    => $right,
            "expected" => $right,
        ];

        yield "some-none" => [
            "left"     => $left,
            "right"    => $none,
            "expected" => $none,
        ];
    }

    /**
     * @return iterable<string, array{
     *   left:Option<string>,
     *   right:Option<string>,
     *   expected:Option<string>
     * }>
     */
    public static function orMatrix(): iterable
    {
        /** @var Option<string> $none */
        $none = Option\none();
        $left = Option\some("left");
        $right = Option\some("right");

        yield "none-none" => [
            "left"     => $none,
            "right"    => $none,
            "expected" => $none,
        ];

        yield "none-some" => [
            "left"     => $none,
            "right"    => $right,
            "expected" => $right,
        ];

        yield "some-some" => [
            "left"     => $left,
            "right"    => $right,
            "expected" => $left,
        ];

        yield "some-none" => [
            "left"     => $left,
            "right"    => $none,
            "expected" => $left,
        ];
    }

    /**
     * @return iterable<string, array{
     *   left:Option<string>,
     *   right:Option<string>,
     *   expected:Option<string>
     * }>
     */
    public static function xorMatrix(): iterable
    {
        /** @var Option<string> $none */
        $none = Option\none();
        $left = Option\some("left");
        $right = Option\some("right");

        yield "none-none" => [
            "left"     => $none,
            "right"    => $none,
            "expected" => $none,
        ];

        yield "none-some" => [
            "left"     => $none,
            "right"    => $right,
            "expected" => $right,
        ];

        yield "some-some" => [
            "left"     => $left,
            "right"    => $right,
            "expected" => $none,
        ];

        yield "some-none" => [
            "left"     => $left,
            "right"    => $none,
            "expected" => $left,
        ];
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, mixed, 3?:bool}>
     */
    public static function fromValueMatrix(): iterable
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
}
