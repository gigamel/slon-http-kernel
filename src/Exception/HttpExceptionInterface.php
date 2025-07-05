<?php

declare(strict_types=1);

namespace Slon\Http\Kernel\Exception;

interface HttpExceptionInterface
{
    /**
     * @return array<string, list<string>>
     */
    public function getHeaders(): array;
}
