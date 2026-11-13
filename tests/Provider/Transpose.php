<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Provider;

use TH\Maybe\Option;
use TH\Maybe\Result;

trait Transpose
{
    /**
     * @return iterable<string, array{option: Option<mixed>, expected: Result<mixed, mixed>}>
     */
    public static function transposeMatrix(): iterable
    {
        yield 'none' => [
            'option' => Option\none(),
            'expected' => Result\ok(Option\none()),
        ];

        yield 'some-ok' => [
            'option' => Option\some(Result\ok(42)),
            'expected' => Result\ok(Option\some(42)),
        ];

        yield 'some-err' => [
            'option' => Option\some(Result\err("Don't panic !")),
            'expected' => Result\err("Don't panic !"),
        ];
    }
}
