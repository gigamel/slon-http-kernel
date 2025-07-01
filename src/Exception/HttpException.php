<?php

declare(strict_types=1);

namespace Slon\Http\Kernel\Exception;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

use function sprintf;

class HttpException extends RuntimeException
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $message = '',
        int $code = 500,
        ?Throwable $previous = null,
    ) {
        if ($code < 200 || $code > 599) {
            throw new InvalidArgumentException(sprintf(
                'Invalid HTTP code [%d]',
                $code,
            ));
        }
        
        parent::__construct($message, $code, $previous);
    }
}
