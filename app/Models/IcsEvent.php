<?php

namespace App\Models;

use App\Exceptions\NoSummaryException;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $token_usage
 * @property string|null $prompt
 * @property string|null $error
 * @property string|null $ics
 * @property string|null $timezone
 * @property string|null $secret
 * @property DateTime $created_at
 * @property DateTime $updated_at
 * @property DateTime|null $deleted_at
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
        'secret',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function is_successful(): bool
    {
        return $this->ics && !$this->error;
    }

    /**
     * @throws NoSummaryException
     */
    public function getSummary(): string
    {
        if (!$this->ics) {
            throw new NoSummaryException("The event {$this->id} has no ics data to create a summary.");
        }

        $icsData = explode('BEGIN:VEVENT', $this->ics);

        $summary = '';
        foreach ($icsData as $index => $value) {
            if ($index === 0) {
                continue;
            }

            if (preg_match('/SUMMARY:(.*)/', $value, $matches)) {
                if ($summary === '') {
                    $summary .= trim($matches[1]);
                } else {
                    $summary .= ', '.trim($matches[1]);
                }
            }

        }

        return $summary;
    }
}
