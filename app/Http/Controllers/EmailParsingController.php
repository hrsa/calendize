<?php

namespace App\Http\Controllers;

use App\Data\MailgunEmail;
use App\Services\MailProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmailParsingController extends Controller
{
    public function __invoke(Request $request, MailProcessingService $service)
    {
        $email = MailgunEmail::from($request->all());

        if (Str::contains($email->recipient, ['ics@calendize.it', 'hey@calendize.it'])) {
            $service->process($email);
        }

        if (Str::contains($email->recipient, ['contact@calendize.it', 'cally@calendize.it'])) {
            $service->forwardToAdmin($email);
        }

        return response()->noContent();
    }
}
