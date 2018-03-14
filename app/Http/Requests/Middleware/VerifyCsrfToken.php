<?php

namespace App\Http\Middleware;
use Closure;
use Redirect;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/segment/merge',
        '/segment/split',
        '/SB/meger',
        'MergeRMB',
        '/canvas',
        '/user/budget_simulation/init/road',
        '/user/budget_simulation/dataset_import',
        'user/budget_simulation/init/deterioration',
        '/user/deterioration/init',
        "ajax/*",
        '/ajax/route',
        '/ajax/road_class',
        '/ajax/repair_method',
        '/ajax/pavement_type',
    ];

    public function handle($request, Closure $next)
    {
        if (
            $this->isReading($request) ||
            $this->runningUnitTests() ||
            $this->shouldPassThrough($request) ||
            $this->tokensMatch($request)
        ) 
        {
            return $this->addCookieToResponse($request, $next($request));
        }

        // redirect the user back to the last page and show error
        return Redirect::back()->withError('Sorry, we could not verify your request. Please try again.');
    }
}
