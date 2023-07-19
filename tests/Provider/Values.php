<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Provider;

use TH\Maybe\Option;

trait Values
{
    /**
     * @return iterable<string, array{mixed}>
     */
    public function values(): iterable
    {
        yield "null" => [null];
        yield "int" => [42];
        yield "string" => ["H2G2"];
        yield "object" => [(object)[]];
        yield "datetime" => [new \DateTimeImmutable()];
    }

    /**
     * @return iterable<array{Option<mixed>, mixed, mixed, 3?:bool}>
     */
    public function fromValueMatrix(): iterable
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
