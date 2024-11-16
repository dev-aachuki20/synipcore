<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLanguage
{
    public function handle($request, Closure $next)
    {

        $user = $request->user();
        if ($this->isApiRequest($request)) {
            if ($user) {
                $newLocale = $user->userLanguage->code ?? 'en';

                $currentLocale = App::getLocale();
                if ($currentLocale != $newLocale && in_array($newLocale, ['en', 'cn', 'jp'])) {
                    App::setLocale($newLocale);
                }
            }
        } else {
            $locale = Session::get('locale', 'cn');  // Default to Traditional Chinese cn
            App::setLocale($locale);
        }
        

        return $next($request);
    }


    private function isApiRequest(Request $request)
    {
        return strpos($request->path(), 'api/') === 0
            || $request->is('api/*')
            || $request->header('Accept') === 'application/json';
    }
}
