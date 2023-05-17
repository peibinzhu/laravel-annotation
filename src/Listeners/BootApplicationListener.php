<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Listeners;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;

class BootApplicationListener
{
    public function __construct(protected Container $container)
    {
    }

    public function handle(object $event): void
    {
        $config = $this->container->get(Repository::class);

        // Load the config/dependencies.php
        if (file_exists(config_path('dependencies.php'))) {
            $dependencies = include config_path('dependencies.php');
            foreach ($dependencies as $abstract => $concrete) {
                $concrete = $concrete ?: $abstract;
                $concreteStr = is_string($concrete) ? $concrete : gettype($concrete);
                if (is_string($concrete) && method_exists($concrete, '__invoke')) {
                    $concrete = function () use ($concrete) {
                        return $this->container->call($concrete . '@__invoke');
                    };
                }
                $this->container->singleton($abstract, $concrete);
                $config->set(sprintf('dependencies.%s', $abstract), $concreteStr);
            }
        }

        // The bindings listed below will be preloaded, avoiding repeated instantiation.
        $warmServices = $config->get('dependencies', []);
        foreach ($warmServices as $abstract => $concrete) {
            if ($this->container->bound($abstract)) {
                $this->container->get($abstract);
            }
        }
    }
}
