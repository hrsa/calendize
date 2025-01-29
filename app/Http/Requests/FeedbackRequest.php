<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'like' => ['required', 'boolean'],
            'ics_event_id' => ['required', 'exists:ics_events,id'],
            'data' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
