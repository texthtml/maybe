<?php declare(strict_types=1);

namespace TH\Maybe\Result;

final class UnusedResultException extends \LogicException
{
    public function __construct(ResultCreationTrace $trace)
    {
        parent::__construct("Unused Result dropped", previous: $trace);
    }
}
