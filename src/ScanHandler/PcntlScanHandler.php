<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\ScanHandler;

use PeibinLaravel\Di\Contracts\ScanHandler;
use PeibinLaravel\Di\Exception\Exception;

class PcntlScanHandler implements ScanHandler
{
    public function __construct()
    {
        if (!extension_loaded('pcntl')) {
            throw new Exception('Missing pcntl extension.');
        }
        if (extension_loaded('grpc')) {
            $grpcForkSupport = ini_get_all('grpc')['grpc.enable_fork_support']['local_value'];
            $grpcForkSupport = strtolower(trim(str_replace('0', '', $grpcForkSupport)));
            if (in_array($grpcForkSupport, ['', 'off', 'false'], true)) {
                throw new Exception(
                    ' Grpc fork support must be enabled before the server starts, please set grpc.enable_fork_support = 1 in your php.ini.'
                );
            }
        }
    }

    public function scan(): Scanned
    {
        $pid = pcntl_fork();
        if ($pid == -1) {
            throw new Exception('The process fork failed');
        }

        if ($pid) {
            pcntl_wait($status);
            return new Scanned(true);
        }

        return new Scanned(false);
    }
}
