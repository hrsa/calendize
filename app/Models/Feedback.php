<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $ics_event_id
 * @property bool $like
 * @property string $data
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property DateTime|null $deleted_at
 * @property User $user
 */
class Feedback extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'like',
        'ics_event_id',
        'data',
    ];

    public function icsEvent(): BelongsTo
    {
        return $this->belongsTo(IcsEvent::class);
    }

    public function user(): BelongsTo
    {
        return $this->icsEvent->user();
    }

    protected function casts(): array
    {
        return [
            'like' => 'boolean',
        ];
    }
}
