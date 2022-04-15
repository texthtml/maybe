<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Provider;

use TH\Maybe\Result;

trait Results
{
    /**
     * @return iterable<string, array{
     *   left:Result<string, null>,
     *   right:Result<string, null>,
     *   expected:Result<string, null>
     * }>
     */
    public function andMatrix(): iterable
    {
        yield "err-err" => [
            "left"     => Result\err("left"),
            "right"    => Result\err("left"),
            "expected" => Result\err("left"),
        ];

        yield "err-ok" => [
            "left"     => Result\err("left"),
            "right"    => Result\ok("right"),
            "expected" => Result\err("left"),
        ];

        yield "ok-ok" => [
            "left"     => Result\ok("left"),
            "right"    => Result\ok("right"),
            "expected" => Result\ok("right"),
        ];

        yield "ok-err" => [
            "left"     => Result\ok("left"),
            "right"    => Result\err("right"),
            "expected" => Result\err("right"),
        ];
    }

    /**
     * @return iterable<string, array{
     *   left:Result<string, null>,
     *   right:Result<string, null>,
     *   expected:Result<string, null>
     * }>
     */
    public function orMatrix(): iterable
    {
        yield "err-err" => [
            "left"     => Result\err("left"),
            "right"    => Result\err("right"),
            "expected" => Result\err("right"),
        ];

        yield "err-ok" => [
            "left"     => Result\err("left"),
            "right"    => Result\ok("right"),
            "expected" => Result\ok("right"),
        ];

        yield "ok-ok" => [
            "left"     => Result\ok("left"),
            "right"    => Result\ok("right"),
            "expected" => Result\ok("left"),
        ];

        yield "ok-err" => [
            "left"     => Result\ok("left"),
            "right"    => Result\err("right"),
            "expected" => Result\ok("left"),
        ];
    }
}
