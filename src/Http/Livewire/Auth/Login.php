<?php

namespace Filament\Http\Livewire\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Filament\Traits\ThrottlesLogins;

class Login extends Component
{
    use ThrottlesLogins;

    public $email;
    public $password;

    public function login(Request $request)
    {
        $data = $this->validate([
            $this->username() => 'required|email',
            'password' => 'required|min:8',
        ]);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if (Auth::attempt($data)) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        $this->addError('password', trans('auth.failed'));
    }
    
    public function render()
    {
        return view('filament::livewire.auth.login')
            ->layout('filament::layouts.auth');
    }
}