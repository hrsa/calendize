<?php

namespace App\Enums;

enum LemonSqueezyProduct: string
{
    case Beginner = 'beginner';
    case Classic = 'classic';
    case Power = 'power';
    case TopUp = 'credits';

    public function product(): string
    {
        return match ($this) {
            self::Beginner => config('lemon-squeezy.sales.beginner.product'),
            self::Classic  => config('lemon-squeezy.sales.classic.product'),
            self::Power    => config('lemon-squeezy.sales.power.product'),
            self::TopUp    => config('lemon-squeezy.sales.topup.product')
        };
    }

    public function variant(): string
    {
        return match ($this) {
            self::Beginner => config('lemon-squeezy.sales.beginner.variant'),
            self::Classic  => config('lemon-squeezy.sales.classic.variant'),
            self::Power    => config('lemon-squeezy.sales.power.variant'),
            self::TopUp    => config('lemon-squeezy.sales.topup.variant')
        };
    }

    public function credits(): int
    {
        return match ($this) {
            self::Beginner => config('lemon-squeezy.sales.beginner.credits'),
            self::Classic  => config('lemon-squeezy.sales.classic.credits'),
            self::Power    => config('lemon-squeezy.sales.power.credits'),
            self::TopUp    => config('lemon-squeezy.sales.topup.credits')
        };
    }

    public function rollover(): ?int
    {
        return match ($this) {
            self::Beginner => config('lemon-squeezy.sales.beginner.rollover'),
            self::Classic  => config('lemon-squeezy.sales.classic.rollover'),
            self::Power    => config('lemon-squeezy.sales.power.rollover'),
            self::TopUp    => null
        };
    }

    public static function subscriptions(): array
    {
        return [self::Beginner, self::Classic, self::Power];
    }
}
