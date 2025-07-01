<?php

declare(strict_types=1);

namespace Slon\Http\Kernel\Container;

use Psr\Container\ContainerInterface;

interface ContainerFactoryInterface
{
    public function makeContainer(): ContainerInterface;
}
