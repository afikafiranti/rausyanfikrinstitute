<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','string','lowercase','email','max:255','unique:'.User::class],
            'password' => [
                'required','confirmed','min:8', // pastikan ada input password_confirmation
                'regex:/[A-Z]/',     // minimal 1 huruf besar
                'regex:/[a-z]/',     // minimal 1 huruf kecil
                'regex:/[0-9]/',     // minimal 1 angka
            ],
        ],[
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, dan angka.',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
