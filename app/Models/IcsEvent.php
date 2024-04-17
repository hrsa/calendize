<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $token_usage
 * @property string|null $prompt
 * @property string|null $error
 * @property string|null $ics
 * @property string|null $timezone
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property DateTime|null $deleted_at
 *
 * @property User $user
 */
class IcsEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'token_usage',
        'prompt',
        'error',
        'ics',
        'timezone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function is_successful(): bool
    {
        return $this->ics && !$this->error;
    }
}
