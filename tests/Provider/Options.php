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
    public function andMatrix(): iterable
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
    public function orMatrix(): iterable
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
    public function xorMatrix(): iterable
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
}
