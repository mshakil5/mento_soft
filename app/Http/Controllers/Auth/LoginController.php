<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\LoginRecord;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->status != 1) {
                return back()->withInput($request->only('email'))
                            ->withErrors(['email' => 'Your user account is inactive.']);
            }

            if (auth()->attempt(['email' => $email, 'password' => $password])) {
                LoginRecord::create(['user_id' => auth()->id()]);

                if ($user->is_type == 1) return redirect()->route('admin.dashboard');
                if ($user->is_type == 2) return redirect()->route('manager.dashboard');
                if ($user->is_type == 3) return redirect()->route('user.dashboard');
            }

            return back()->withInput($request->only('email'))->withErrors(['password' => 'Wrong Password.']);
        }

          return back()->withInput($request->only('email'))->withErrors(['email' => 'Credential Error.']);

    }
}
