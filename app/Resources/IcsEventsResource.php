<?php

namespace App\Resources;

use App\Exceptions\NoSummaryException;
use App\Models\IcsEvent;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/** @mixin IcsEvent */
class IcsEventsResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array{'id': int, 'summary': string, 'error': string|null, 'ics': string|null, 'created_at': DateTime, 'token_usage': int|null, 'secret': string|null }
     * @throws NoSummaryException
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'summary'     => $this->ics ? $this->getSummary() : 'error',
            'error'       => $this->error,
            'ics'         => $this->ics,
            'created_at'  => $this->created_at,
            'token_usage' => $this->token_usage,
            'secret'      => $this->secret,
        ];
    }
}
