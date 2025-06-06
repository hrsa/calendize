<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\LemonSqueezyProduct;
use App\Observers\UserObserver;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;
use LemonSqueezy\Laravel\Billable;
use LemonSqueezy\Laravel\Subscription;

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
 * @property Carbon|null $deleted_at
 * @property int $failed_requests
 * @property bool $has_password
 * @property string|null $remember_token
 * @property string|null $provider
 * @property string|null $provider_id
 * @property string|null $telegram_id
 * @property bool $send_tg_notifications
 * @property Carbon|null $hide_pw_reminder
 * @property string|null $timezone
 * @property IcsEvent $icsEvents
 */
#[ObservedBy(UserObserver::class)]
class User extends Authenticatable implements MustVerifyEmail
{
    use Billable, HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'provider',
        'provider_id',
        'hide_pw_reminder',
        'telegram_id',
        'send_tg_notifications',
        'timezone',
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

    protected $appends = ['is_admin', 'active_subscription', 'days_since_password_reminder'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function icsEvents(): HasMany
    {
        return $this->hasMany(IcsEvent::class);
    }

    public function processedIcsEvents(): HasMany
    {
        return $this->icsEvents()->processed();
    }

    public function pendingIcsEvents(): HasMany
    {
        return $this->icsEvents()->pending();
    }

    public function latestIcsEvent(): HasOne
    {
        return $this->hasOne(IcsEvent::class)->latest();
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->email === config('app.admin.email');
    }

    public function getActiveSubscriptionAttribute(): string
    {
        /** @var Subscription|null $subscription */
        $subscription = $this->subscriptions()->whereStatus(Subscription::STATUS_ACTIVE)->first();

        if ($subscription instanceof Subscription) {
            foreach (LemonSqueezyProduct::cases() as $product) {
                if ($subscription->hasVariant($product->variant())) {
                    return $product->value;
                }
            }
        }

        return 'none';
    }

    public function getDaysSincePasswordReminderAttribute(): int
    {
        if (!$this->has_password) {
            return $this->hide_pw_reminder ? (int) today()->diffInDays($this->hide_pw_reminder, true) : 8;
        }

        return 0;
    }
}
