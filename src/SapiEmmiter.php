<?php

declare(strict_types=1);

namespace Slon\Http\Kernel;

use Psr\Http\Message\ResponseInterface;
use Slon\Http\Kernel\Contract\SapiEmmiterInterface;
use Slon\Http\Kernel\Exception\HeadersAlreadySentException;

use function array_keys;
use function header;
use function headers_sent;
use function ob_end_clean;
use function ob_get_level;
use function sprintf;

class SapiEmmiter implements SapiEmmiterInterface
{
    // Todo: partially emmit body
    
    /**
     * @inheritDoc
     */
    public function emmit(ResponseInterface $response): void
    {
        if (headers_sent()) {
            throw new HeadersAlreadySentException(
                'Response HTTP headers already sent',
            );
        }
        
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        foreach (array_keys($response->getHeaders()) as $key) {
            header(sprintf('%s: %s', $key, $response->getHeaderLine($key)));
        }
        
        header(
            sprintf(
                'HTTP/%s %d %s',
                $response->getProtocolVersion(),
                $response->getStatusCode(),
                $response->getReasonPhrase(),
            ),
            true,
            $response->getStatusCode(),
        );
        
        echo $response->getBody()->getContents();
    }
}
