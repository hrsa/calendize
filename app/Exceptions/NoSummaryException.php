<?php

namespace App\Exceptions;

use App\Models\IcsEvent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class NoSummaryException extends Exception
{
    public function __construct(public IcsEvent $icsEvent) {}

    public function report(): void
    {
        Log::error("Can't get summary on IcsEvent {$this->icsEvent->id}");
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): Response
    {
        return response("IcsEvent {$this->icsEvent->id} has no summary", SymfonyResponse::HTTP_BAD_REQUEST);
    }
}
