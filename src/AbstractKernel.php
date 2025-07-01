<?php

declare(strict_types=1);

namespace Slon\Http\Kernel;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Slon\Http\Kernel\Contract\ApplicationInterface;
use Slon\Http\Kernel\Contract\SapiEmmiterInterface;
use Slon\Http\Kernel\Exception\HttpException;
use Throwable;
use function php_sapi_name;
use function sprintf;

abstract class AbstractKernel
{
    /**
     * @throw RuntimeException
     */
    public function __construct()
    {
        if ('cli' === php_sapi_name()) {
            throw new RuntimeException(sprintf(
                'HTTP application cannot be run in SAPI "%s"',
                php_sapi_name(),
            ));
        }
    }
    
    public function run(): void
    {
        $this->doRun();
    }
    
    abstract protected function getContainer(): ContainerInterface;
    
    abstract protected function getServerRequest(): ServerRequestInterface;
    
    /**
     * @throws Throwable
     */
    protected function doRun(): void
    {
        $container = $this->getContainer();
        
        /** @var ApplicationInterface $application */
        $application = $container->get(ApplicationInterface::class);
        
        $request = $this->getServerRequest();
        
        // Start: need to change
        $afterRequest = $application->handle($request);
        if (!$handler = $afterRequest->getAttribute('_handler')) {
            throw new HttpException('Not found handler', 404);
        }
        if ($container->has($handler)) {
            $controller = $container->get($handler);
        } else {
            $controller = new $handler();
        }
        $response = $controller($request);
        // End: need to change
        
        /** @var SapiEmmiterInterface $emmiter */
        $emmiter = $container->get(SapiEmmiterInterface::class);
        
        $emmiter->emmit($response);
    }
}
