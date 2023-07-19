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
}
