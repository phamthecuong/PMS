<?php namespace App\Http\Middleware;

use Closure;
use Session;
use App;
use Config;

class Language {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!\Session::has('locale'))
        {
           \Session::put('locale', \Config::get('app.locale'));
        }
        $language;
        if ($request->is('*language*'))
        {
            $language = $request->lang;
        }
        else
        {
            $language = Session::get('locale', Config::get('app.locale'));    
        }

        App::setLocale($language);

        return $next($request);
    }

}