<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Rules\IsActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{

    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentialsOnly = $request->validate([
            'email'    => ['required', 'email', 'regex:/^(?!.*[\/]).+@(?!.*[\/]).+\.(?!.*[\/]).+$/i', 'exists:users,email,deleted_at,NULL', new IsActive],
            'password' => 'required|min:8',
        ], [
            'email.exists' => trans('validation.invalid'),
        ]);


        $user = User::where('email', $request->email)->first();

        if ($user && $user->is_admin || $user && $user->is_sub_admin) {
            $remember_me = !is_null($request->remember_me) ? true : false;
            if (Auth::attempt($credentialsOnly, $remember_me)) {
                $routeName = 'admin.dashboard';

                return redirect()->route($routeName)->with('success', trans('auth.messages.login.success'));
            }
            return redirect()->route('login')->with('error', trans('auth.failed'));
        } else {
            return redirect()->route('login')->with('error', trans('auth.messages.login.failed'));
        }
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        Session::forget('locale');
        return redirect()->route('login');
    }


    public function showAdminLogin()
    {
        return view('backend.auth.login');
    }
}
