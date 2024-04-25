<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Observers\UserObserver;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use LemonSqueezy\Laravel\Billable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int $credits
 * @property int|null $rollover_credits
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $failed_requests
 * @property bool $has_password
 * @property string|null $remember_token
 * @property IcsEvent $icsEvents
 */
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements MustVerifyEmail
{
    use Billable, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'credits',
        'rollover_credits',
        'email_verified_at',
        'failed_requests',
        'has_password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['active_subscription'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function icsEvents(): HasMany
    {
        return $this->hasMany(IcsEvent::class);
    }

    public function hasTooManyErrors(): bool
    {
        return $this->failed_requests > $this->credits;
    }

    public function getActiveSubscriptionAttribute(): string
    {
        return match ($this->subscriptions()->active()?->first()?->variant_id) {
            config('lemon-squeezy.sales.beginner.variant') => 'beginner',
            config('lemon-squeezy.sales.classic.variant') => 'classic',
            config('lemon-squeezy.sales.power.variant') => 'power',
            default => 'none',
        };
    }
}
