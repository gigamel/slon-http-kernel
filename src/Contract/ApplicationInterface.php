<?php

declare(strict_types=1);

namespace Slon\Http\Kernel\Contract;

use Psr\Http\Message\ServerRequestInterface;

interface ApplicationInterface
{
    public function handle(
        ServerRequestInterface $request,
    ): ServerRequestInterface;
}
