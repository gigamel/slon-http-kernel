<?php

declare(strict_types=1);

namespace Slon\Http\Kernel\Configuration;

use const GLOB_BRACE;

use RuntimeException;

use function array_replace_recursive;
use function glob;
use function is_array;
use function is_file;
use function sprintf;

final class ArrayProvider
{
    private iterable $patterns;

    public function __construct(string ...$patterns)
    {
        $this->patterns = $patterns;
    }
    
    public function getArray(): array
    {
        $configs = [];
        foreach ($this->patterns as $pattern) {
            if (is_file($pattern)) {
                $configs = array_replace_recursive(
                    $configs,
                    $this->getConfigs($pattern),
                );
                
                continue;
            }
            
            foreach (glob($pattern, GLOB_BRACE) as $file) {
                $configs = array_replace_recursive(
                    $configs,
                    $this->getConfigs($file),
                );
            }
        }
        
        return $configs;
    }
    
    private function getConfigs(string $config): array
    {
        $configs = require_once($config);
        if (false === $configs) {
            throw new RuntimeException(sprintf(
                'Configs "%s" already imported',
                $configs,
            ));
        }
        
        if (!is_array($configs)) {
            throw new RuntimeException(sprintf(
                'File "%s" must returns Closure',
                $config,
            ));
        }
        
        return $configs;
    }
}
