<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Contracts;

use PeibinLaravel\Di\ScanHandler\Scanned;

interface ScanHandlerInterface
{
    public function scan(): Scanned;
}
