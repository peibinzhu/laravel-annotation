<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
