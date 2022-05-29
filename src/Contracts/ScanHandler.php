<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Contracts;

use PeibinLaravel\Di\ScanHandler\Scanned;

interface ScanHandler
{
    public function scan(): Scanned;
}
