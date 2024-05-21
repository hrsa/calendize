<?php

namespace App\Resources;

use App\Models\IcsEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin IcsEvent */
class IcsEventsResource extends JsonResource
{
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
