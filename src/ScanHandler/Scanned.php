<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\ScanHandler;

class Scanned
{
    public function __construct(protected bool $scanned)
    {
    }

    public function isScanned(): bool
    {
        return $this->scanned;
    }
}
