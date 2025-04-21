<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\ObjectReaper\Reaper;

/** @internal */
trait MustBeUsed
{
    /** @return \WeakMap<static,Reaper> */
    private static function reapers(): \WeakMap
    {
        static $reapers;

        return $reapers ??= new \WeakMap();
    }

    /**
     * Mark a result as needed to be used. Must be called at most once on an object.
     */
    private function mustBeUsed(): void
    {
        $reapers = self::reapers();

        if (isset($reapers[$this])) {
            throw new \LogicException('Object already register to be used');
        }

        $creationTrace = new ResultCreationTrace();

        $reapers[$this] = Reaper::watch($this, static fn () => throw new UnusedResultException($creationTrace));
    }

    /**
     * Mark a result as used.
     */
    private function used(): void
    {
        $reaper = self::reapers()[$this] ?? throw new \LogicException('Object not registered to be used');
        $reaper->forget();
    }

    public function __clone()
    {
        $this->mustBeUsed();
    }
}
