<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class AutoFixPermissions
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);
            return $response;
        } catch (\Exception $e) {
            // Check if it's a permission error
            if ($this->isPermissionError($e)) {
                Log::warning('Permission error detected, attempting auto-fix', [
                    'error' => $e->getMessage(),
                    'path' => $this->extractPath($e->getMessage())
                ]);
                
                // Try to fix permissions
                $this->fixPermissions();
                
                // Retry the request once
                try {
                    return $next($request);
                } catch (\Exception $retryException) {
                    Log::error('Permission fix failed, manual intervention needed', [
                        'original_error' => $e->getMessage(),
                        'retry_error' => $retryException->getMessage()
                    ]);
                    throw $retryException;
                }
            }
            
            throw $e;
        }
    }
    
    private function isPermissionError(\Exception $e): bool
    {
        $message = $e->getMessage();
        return strpos($message, 'Permission denied') !== false
            || strpos($message, 'file_put_contents') !== false
            || strpos($message, 'Failed to open stream') !== false;
    }
    
    private function extractPath(string $errorMessage): ?string
    {
        if (preg_match('/file_put_contents\(([^)]+)\)/', $errorMessage, $matches)) {
            return $matches[1];
        }
        return null;
    }
    
    private function fixPermissions(): void
    {
        try {
            // Run the fix-permissions script
            $process = new Process([
                '/bin/bash',
                base_path('fix-permissions.sh')
            ]);
            
            $process->run();
            
            if (!$process->isSuccessful()) {
                Log::error('Auto permission fix failed', [
                    'output' => $process->getOutput(),
                    'error' => $process->getErrorOutput()
                ]);
            } else {
                Log::info('Permissions auto-fixed successfully');
            }
        } catch (\Exception $e) {
            Log::error('Exception during auto permission fix', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
