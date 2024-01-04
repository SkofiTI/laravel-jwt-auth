<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class RateLimitExceededException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'error' => 'Rate limit exceeded.',
            'message' => $this->getMessage(),
        ], 429);
    }
}
