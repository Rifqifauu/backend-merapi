<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $levels = [];
    protected $dontReport = [];
    protected $dontFlash = ['current_password', 'password', 'password_confirmation'];

    public function register(): void
    {
        //
    }

    /**
     * Tangani request yang tidak terautentikasi.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // Selalu kembalikan JSON 401
        return response()->json([
            'message' => 'Belum login'
        ], 401);
    }
}
