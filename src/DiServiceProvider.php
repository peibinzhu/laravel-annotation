<?php

declare(strict_types=1);

namespace PeibinLaravel\Di;

use Illuminate\Support\ServiceProvider;
use PeibinLaravel\Di\Annotation\AnnotationCollector;
use PeibinLaravel\ProviderConfig\Contracts\ProviderConfigInterface;

class DiServiceProvider extends ServiceProvider implements ProviderConfigInterface
{
    public function __invoke(): array
    {
        return [
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
            'publish'     => [
                [
                    'id'          => 'annotation-config',
                    'source'      => __DIR__ . '/../config/annotations.php',
                    'destination' => config_path('annotations.php'),
                ],
            ],
        ];
    }
}
