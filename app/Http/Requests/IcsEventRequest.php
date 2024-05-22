<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IcsEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'calendarEvent' => ['required', 'string'],
            'timeZone'      => ['nullable'],
            'email'         => ['nullable', 'email'],
        ];
    }

    public function messages(): array
    {
        return [
            'calendarEvent.required' => "The calendar event field can't be empty.",
        ];
    }
}
