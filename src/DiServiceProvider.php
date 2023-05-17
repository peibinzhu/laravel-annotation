<?php

declare(strict_types=1);

namespace PeibinLaravel\Di;

use Illuminate\Console\Events\ArtisanStarting;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Laravel\Octane\Events\MainServerStarting;
use PeibinLaravel\Di\Annotation\AnnotationCollector;
use PeibinLaravel\Di\Listeners\BootApplicationListener;

class DiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerConfig();

        $listeners = [
            ArtisanStarting::class    => BootApplicationListener::class,
            MainServerStarting::class => BootApplicationListener::class,
        ];
        $this->registerlisteners($listeners);

        $this->registerPublishing();
    }

    private function registerConfig()
    {
        $items = [
            'annotations' => [
                'scan' => [
                    'paths'      => [
                        __DIR__,
                    ],
                    'collectors' => [
                        AnnotationCollector::class,
                    ],
                ],
            ],
        ];
        $config = $this->app->get(Repository::class);
        foreach ($items as $key => $value) {
            $value = array_merge_recursive($config->get($key, []), $value);
            $config->set($key, $value);
        }
    }

    private function registerListeners(array $listeners)
    {
        $dispatcher = $this->app->get(Dispatcher::class);
        foreach ($listeners as $event => $_listeners) {
            foreach ((array)$_listeners as $listener) {
                $dispatcher->listen($event, $listener);
            }
        }
    }

    public function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/annotations.php' => config_path('annotations.php'),
            ], 'annotation-config');
        }
    }
}
