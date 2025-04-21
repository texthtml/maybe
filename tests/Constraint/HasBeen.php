<?php declare(strict_types=1);

namespace TH\Maybe\Tests\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use TH\Maybe\Result;
use TH\ObjectReaper\Reaper;

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
     * @template T of Result<mixed,mixed>
     * @param T $result
     */
    protected function toBeUsed(Result $result): bool
    {
        $ro = new \ReflectionObject($result);

        /** @throws void */
        $reapers = $ro->getMethod('reapers')->invoke(null);
        /** @var \WeakMap<T,Reaper> $reapers */
        $reaper = $reapers[$result];

        $ro = new \ReflectionObject($reaper);

        /** @throws void */
        $rp = $ro->getProperty('active');
        /** @psalm-suppress UnusedMethodCall */
        $rp->setAccessible(true);

        $result = $rp->getValue($reaper);

        \assert(\is_bool($result));

        return $result;
    }
}
