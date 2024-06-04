<?php

namespace App\Enums;

use Illuminate\Support\Facades\Config;

enum LemonSqueezyProduct: string
{
    case Beginner = 'beginner';
    case Classic = 'classic';
    case Power = 'power';
    case TopUp = 'credits';

    public function product(): string
    {
        return match ($this) {
            self::Beginner => Config::string('lemon-squeezy.sales.beginner.product'),
            self::Classic  => Config::string('lemon-squeezy.sales.classic.product'),
            self::Power    => Config::string('lemon-squeezy.sales.power.product'),
            self::TopUp    => Config::string('lemon-squeezy.sales.topup.product')
        };
    }

    public function variant(): string
    {
        return match ($this) {
            self::Beginner => Config::string('lemon-squeezy.sales.beginner.variant'),
            self::Classic  => Config::string('lemon-squeezy.sales.classic.variant'),
            self::Power    => Config::string('lemon-squeezy.sales.power.variant'),
            self::TopUp    => Config::string('lemon-squeezy.sales.topup.variant')
        };
    }

    public function credits(): int
    {
        return match ($this) {
            self::Beginner => Config::integer('lemon-squeezy.sales.beginner.credits'),
            self::Classic  => Config::integer('lemon-squeezy.sales.classic.credits'),
            self::Power    => Config::integer('lemon-squeezy.sales.power.credits'),
            self::TopUp    => Config::integer('lemon-squeezy.sales.topup.credits')
        };
    }

    public function rollover(): ?int
    {
        return match ($this) {
            self::Beginner => Config::integer('lemon-squeezy.sales.beginner.rollover'),
            self::Classic  => Config::integer('lemon-squeezy.sales.classic.rollover'),
            self::Power    => Config::integer('lemon-squeezy.sales.power.rollover'),
            self::TopUp    => null
        };
    }

    public static function subscriptions(): array
    {
        return [self::Beginner, self::Classic, self::Power];
    }
}
