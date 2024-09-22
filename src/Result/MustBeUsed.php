<?php declare(strict_types=1);

namespace TH\Maybe\Result;

use TH\Maybe\Result;

/**
 * @internal
 * @nodoc
 */
trait MustBeUsed
{
    /** @var \ArrayAccess<Result<mixed,mixed>,ResultCreationTrace>|null */
    private static ?\ArrayAccess $toBeUsed = null;

    /**
     * @return \ArrayAccess<Result<mixed,mixed>,ResultCreationTrace>
     */
    private static function toBeUsedMap(): \ArrayAccess
    {
        return self::$toBeUsed ??= self::emptyToBeUsedMap();
    }

    /**
     * @return \WeakMap<Result<mixed,mixed>,ResultCreationTrace>
     */
    private static function emptyToBeUsedMap(): \WeakMap
    {
        /** @var \WeakMap<Result<mixed,mixed>,ResultCreationTrace> */
        return new \WeakMap();
    }

    /**
     * Mark a result as needed to be used
     */
    private function mustBeUsed(): void
    {
        self::toBeUsedMap()[$this] = new ResultCreationTrace();
    }

    /**
     * Mark a result as used
     */
    private function used(): void
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
