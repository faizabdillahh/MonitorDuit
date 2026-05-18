<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings', [
            'user' => auth()->user()->only(['name', 'email', 'dark_mode_preference', 'timezone']),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'dark_mode_preference' => ['required', 'in:system,light,dark'],
            'timezone'             => ['required', 'string', 'max:50'],
        ]);

        auth()->user()->update($request->only(['dark_mode_preference', 'timezone']));

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
