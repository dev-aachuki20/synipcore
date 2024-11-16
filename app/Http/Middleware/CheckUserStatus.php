<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            
            $user = User::where('id',auth()->user()->id)->where('status', 0)->first();
          
            if ($user) {
                
                if(!$user->is_admin){
                    $request->user()->tokens()->delete();
                    return response()->json([
                        'status'        => false,
                        'status_code'   => 401,
                        'error'         => trans('messages.user_account_deactivate')
                    ],401);
                }
                
            }
        }

        return $next($request);
    }

}
