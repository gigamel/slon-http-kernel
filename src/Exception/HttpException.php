<?php

declare(strict_types=1);

namespace Slon\Http\Kernel\Exception;

use InvalidArgumentException;
use RuntimeException;
use Throwable;

use function sprintf;

class HttpException extends RuntimeException implements HttpExceptionInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $message = '',
        int $code = 500,
        protected array $headers = [], // Todo
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
    
    /**
     * @inheritDoc
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
