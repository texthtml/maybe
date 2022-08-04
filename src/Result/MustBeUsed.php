<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Result;

/** @internal */
trait MustBeUsed
{
    /** @var \ArrayAccess<Result<mixed,mixed>,ResultCreationTrace>|null */
    private static ?\ArrayAccess $toBeUsed = null;

    /**
     * @return \ArrayAccess<Result<mixed,mixed>,ResultCreationTrace>
     */
    private static function toBeUsedMap(): \ArrayAccess
    {
        /** @var \WeakMap<Result<mixed,mixed>,ResultCreationTrace> */
        return self::$toBeUsed ??= new \WeakMap();
    }

    /**
     * Mark a result as needed to be used
     */
    protected function mustBeUsed(): void
    {
        self::toBeUsedMap()[$this] = new ResultCreationTrace();
    }

    /**
     * Mark a result as used
     */
    protected function used(): void
    {
        unset(self::toBeUsedMap()[$this]);
    }

    public function __clone()
    {
        $this->mustBeUsed();
    }

    /**
     * @throws UnusedResultException
     */
    public function __destruct()
    {
        $map = self::toBeUsedMap();

        if (isset($map[$this])) {
            $creationTrace = $map[$this];
            unset($map[$this]);

            throw new UnusedResultException($creationTrace);
        }
    }
}
