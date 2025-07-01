<?php

declare(strict_types=1);

namespace Slon\Http\Kernel;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slon\Http\Kernel\Contract\ApplicationInterface;
use Slon\Http\Kernel\Contract\SapiEmmiterInterface;
use Throwable;
use RuntimeException;

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
        
        $response = $application->handle($request);
        
        /** @var SapiEmmiterInterface $emmiter */
        $emmiter = $container->get(SapiEmmiterInterface::class);
        
        $emmiter->emmit($response);
    }
}
