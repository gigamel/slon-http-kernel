<?php

declare(strict_types=1);

namespace Slon\Http\Kernel\Import;

use const GLOB_BRACE;

use Closure;
use RuntimeException;

use function call_user_func;
use function glob;
use function is_file;
use function sprintf;

final class ClosureImporter
{
    private iterable $patterns;

    public function __construct(string ...$patterns)
    {
        $this->patterns = $patterns;
    }
    
    public function withArgs(...$args): void
    {
        foreach ($this->patterns as $pattern) {
            if (is_file($pattern)) {
                $this->callClosure($pattern, ...$args);
                continue;
            }
            
            foreach (glob($pattern, GLOB_BRACE) as $file) {
                $this->callClosure($file, ...$args);
            }
        }
    }
    
    private function callClosure(string $file, ...$args): void
    {
        $closure = require_once($file);
        if (false === $closure) {
            throw new RuntimeException(sprintf(
                'Closure "%s" already imported',
                $file,
            ));
        }
        
        if (!$closure instanceof Closure) {
            throw new RuntimeException(sprintf(
                'File "%s" must returns Closure',
                $file,
            ));
        }
        
        call_user_func($closure, ...$args);
    }
}
