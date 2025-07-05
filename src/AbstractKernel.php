<?php

declare(strict_types=1);

namespace Slon\Http\Kernel;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Slon\Http\Kernel\Exception\KernelException;
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
        
        $application = $container->get('application');
        if (!$application instanceof RequestHandlerInterface) {
            throw new KernelException(sprintf(
                'Application instance must implements interface "%s"',
                RequestHandlerInterface::class,
            ));
        }
        
        $request = $this->getServerRequest();
        
        $response = $application->handle($request);
        
        $emmiter = $container->get('sapi_emmiter');
        
        $emmiter->emmit($response);
    }
}
