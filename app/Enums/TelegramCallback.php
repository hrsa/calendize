<?php

namespace App\Enums;

enum TelegramCallback: string
{
    case GetEvent = 'downloadEvent';
    case GetEventsOnPage = 'getEventsOnPage';
    case Calendize = 'calendize';
}
