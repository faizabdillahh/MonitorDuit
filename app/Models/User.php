<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dark_mode_preference',
        'timezone',
        'default_currency',
        'notif_budget_warning',
        'notif_budget_exceeded',
        'notif_reminder_email',
        'reminder_idle_days',
        'last_reminded_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'notif_budget_warning' => 'boolean',
            'notif_budget_exceeded'=> 'boolean',
            'notif_reminder_email' => 'boolean',
            'last_reminded_at'     => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::created(function ($user) {
            $defaultCategories = [
                ['name' => 'Makanan', 'icon' => '🍔', 'color' => '#f59e0b'],
                ['name' => 'Transportasi', 'icon' => '🚗', 'color' => '#3b82f6'],
                ['name' => 'Belanja', 'icon' => '🛒', 'color' => '#ec4899'],
                ['name' => 'Tagihan', 'icon' => '🧾', 'color' => '#ef4444'],
                ['name' => 'Hiburan', 'icon' => '🎬', 'color' => '#8b5cf6'],
            ];

            foreach ($defaultCategories as $cat) {
                $user->categories()->create([
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'color' => $cat['color'],
                    'is_default' => false,
                ]);
            }
        });
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function exports(): HasMany
    {
        return $this->hasMany(Export::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }
}
