<?php declare(strict_types=1);

namespace TH\Maybe\Result;

/** @internal */
final class ResultCreationTrace extends \LogicException
{
    public function __construct()
    {
        parent::__construct("Result must be used. Created");
    }
}
