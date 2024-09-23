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
    public static function andMatrix(): iterable
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
    public static function orMatrix(): iterable
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

    /**
     * @return iterable<string, array{class-string<Result<mixed,mixed>>}>
     */
    public static function resultClasses(): iterable
    {
        yield "Ok" => [Result\Ok::class];
        yield "Err" => [Result\Err::class];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function resultMethods(): iterable
    {
        $rc = new \ReflectionClass(Result::class);

        foreach ($rc->getMethods() as $method) {
            yield $method->getName() => [$method->getName()];
        }
    }
}
