<?php

declare(strict_types=1);

namespace PeibinLaravel\Di\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
