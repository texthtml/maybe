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
        $errLeft = Result\err("left");
        $errRight = Result\err("right");
        $okLeft = Result\ok("left");
        $okRight = Result\ok("right");

        yield "err-err" => [
            "left"     => $errLeft,
            "right"    => $errLeft,
            "expected" => $errLeft,
        ];

        yield "err-ok" => [
            "left"     => $errLeft,
            "right"    => $okRight,
            "expected" => $errLeft,
        ];

        yield "ok-ok" => [
            "left"     => $okLeft,
            "right"    => $okRight,
            "expected" => $okRight,
        ];

        yield "ok-err" => [
            "left"     => $okLeft,
            "right"    => $errRight,
            "expected" => $errRight,
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
        $errLeft = Result\err("left");
        $errRight = Result\err("right");
        $okLeft = Result\ok("left");
        $okRight = Result\ok("right");

        yield "err-err" => [
            "left"     => $errLeft,
            "right"    => $errRight,
            "expected" => $errRight,
        ];

        yield "err-ok" => [
            "left"     => $errLeft,
            "right"    => $okRight,
            "expected" => $okRight,
        ];

        yield "ok-ok" => [
            "left"     => $okLeft,
            "right"    => $okRight,
            "expected" => $okLeft,
        ];

        yield "ok-err" => [
            "left"     => $okLeft,
            "right"    => $errRight,
            "expected" => $okLeft,
        ];
    }
}
