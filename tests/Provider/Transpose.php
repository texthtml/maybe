<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Provider;

use TH\Maybe\Option;
use TH\Maybe\Result;

trait Transpose
{
    /**
     * @return iterable<array{option:Option<mixed>, result:Result<mixed, mixed>}>
     */
    public static function transposeMatrix(): iterable
    {
        yield "none" => [
            "option" => Option\none(),
            "result" => Result\ok(Option\none()),
        ];

        yield "some-ok" => [
            "option" => Option\some(Result\ok(42)),
            "result" => Result\ok(Option\some(42)),
        ];

        yield "some-err" => [
            Option\some(Result\err("Don't panic !")),
            Result\err("Don't panic !"),
        ];
    }
}
