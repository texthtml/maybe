<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use TH\Maybe\Result;

final class HasBeen extends Constraint
{
    public function __construct(private bool $used)
    {
    }

    public function toString(): string
    {
        return $this->used
            ? "has been used"
            : "has not been used";
    }

    protected function matches(mixed $other): bool
    {
        if (!$other instanceof Result) {
            return false;
        }

        return $this->toBeUsed($other) !== $this->used;
    }

    /**
     * @param Result<mixed,mixed> $result
     */
    protected function toBeUsed(Result $result): bool
    {
        $ro = new \ReflectionObject($result);

        $rp = $ro->getProperty("toBeUsed");
        $rp->setAccessible(true);

        /** \WeakMap<Result, mixed> $toBeUsedMap */
        $toBeUsedMap = $rp->getValue(null);

        return isset($toBeUsedMap[$result]);
    }
}
