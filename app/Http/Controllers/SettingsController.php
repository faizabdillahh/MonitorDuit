<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings', [
            'user' => auth()->user()->only([
                'name', 'email', 'dark_mode_preference', 'timezone',
                'default_currency',
                'notif_budget_warning', 'notif_budget_exceeded',
                'notif_reminder_email', 'reminder_idle_days',
            ]),
            'supportedCurrencies' => app(\App\Services\CurrencyService::class)->getSupportedCurrencies(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'dark_mode_preference'  => ['sometimes', 'in:system,light,dark'],
            'timezone'              => ['sometimes', 'string', 'max:50'],
            'default_currency'      => ['sometimes', 'string', 'size:3'],
            'notif_budget_warning'  => ['sometimes', 'boolean'],
            'notif_budget_exceeded' => ['sometimes', 'boolean'],
            'notif_reminder_email'  => ['sometimes', 'boolean'],
            'reminder_idle_days'    => ['sometimes', 'integer', 'min:1', 'max:30'],
        ]);

        auth()->user()->update($request->only([
            'dark_mode_preference', 'timezone', 'default_currency',
            'notif_budget_warning', 'notif_budget_exceeded',
            'notif_reminder_email', 'reminder_idle_days',
        ]));

        return back();
    }
}
