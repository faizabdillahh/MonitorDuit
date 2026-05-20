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
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\.\-\']+$/u'],
            'email' => 'required|string|lowercase|email:rfc,strict|max:255|unique:'.User::class,
            'password' => ['required', Rules\Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ], [
            'name.regex' => 'Nama hanya boleh berisi huruf, spasi, titik, tanda hubung, atau tanda petik.',
            'email.email' => 'Format email tidak valid (pastikan tidak ada karakter aneh seperti @@).',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password_confirmation.same' => 'Konfirmasi kata sandi tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
