<?php

namespace App\Enums;

enum TelegramCommand: string
{
    case MyEvents = '/events';
    case NotifyMe = '/notify';
    case DontNotifyMe = '/stop';
    case MyCredits = '/credits';
    case Calendize = '/calendize';
    case Spam = '/spam';
}
