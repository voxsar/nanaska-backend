<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     *
     * SECURITY NOTE: CSRF disabled for API routes per requirements.
     * Students authenticate from different frontend with no CSRF tokens.
     */
    protected $except = [
        'api/*',
    ];
}
