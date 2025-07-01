<?php

declare(strict_types=1);

namespace Slon\Http\Kernel\Contract;

use Psr\Http\Message\ResponseInterface;
use Slon\Http\Kernel\Exception\HeadersAlreadySentException;

interface SapiEmmiterInterface
{
    /**
     * @throws HeadersAlreadySentException
     */
    public function emmit(ResponseInterface $response): void;
}
