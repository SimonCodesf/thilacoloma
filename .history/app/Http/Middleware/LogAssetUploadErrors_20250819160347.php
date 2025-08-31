<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LogAssetUploadErrors
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);
            
            // Log failed asset uploads
            if ($request->is('cp/assets') && $request->isMethod('POST') && $response->getStatusCode() === 422) {
                Log::error('Asset upload failed with 422', [
                    'files' => $request->file(),
                    'data' => $request->all(),
                    'response' => $response->getContent()
                ]);
            }
            
            return $response;
        } catch (ValidationException $e) {
            Log::error('Asset upload validation error', [
                'errors' => $e->errors(),
                'files' => $request->file(),
                'request_data' => $request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Asset upload exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'files' => $request->file()
            ]);
            throw $e;
        }
    }
}
