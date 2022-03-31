<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Annotation;

use PeibinLaravel\Di\Contracts\Annotation as AnnotationContract;
use ReflectionProperty;

abstract class AbstractAnnotation implements AnnotationContract
{
    public function __construct(...$value)
    {
        $formattedValue = $this->formatParams($value);
        foreach ($formattedValue as $key => $val) {
            if (property_exists($this, $key)) {
                $this->{$key} = $val;
            }
        }
    }

    public function toArray(): array
    {
        $properties = ReflectionManager::reflectClass(static::class)->getProperties(ReflectionProperty::IS_PUBLIC);
        $result = [];
        foreach ($properties as $property) {
            $result[$property->getName()] = $property->getValue($this);
        }
        return $result;
    }

    public function collectClass(string $className): void
    {
        AnnotationCollector::collectClass($className, static::class, $this);
    }

    public function collectMethod(string $className, ?string $target): void
    {
        AnnotationCollector::collectMethod($className, $target, static::class, $this);
    }

    public function collectProperty(string $className, ?string $target): void
    {
        AnnotationCollector::collectProperty($className, $target, static::class, $this);
    }

    protected function formatParams($value): array
    {
        if (isset($value[0])) {
            $value = $value[0];
        }
        if (!is_array($value)) {
            $value = ['value' => $value];
        }
        return $value;
    }

    protected function bindMainProperty(string $key, array $value)
    {
        $formattedValue = $this->formatParams($value);
        if (isset($formattedValue['value'])) {
            $this->{$key} = $formattedValue['value'];
        }
    }
}
