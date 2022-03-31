<?php

declare(strict_types=1);

namespace PeibinLaravel\Di;

use Illuminate\Support\ServiceProvider;
use PeibinLaravel\Di\Annotation\ScanConfig;
use PeibinLaravel\Di\Annotation\Scanner;

class DiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerAnnotation();
        $this->registerPublishing();
    }

    private function registerAnnotation()
    {
        $config = ScanConfig::instance(config('annotations'));
        (new Scanner($config))->scan();
    }

    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/annotations.php' => config_path('annotations.php'),
            ], 'annotation-config');
        }
    }
}
