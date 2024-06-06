<?php

namespace App\Livewire;

use App\Models\PageView;
use Laravel\Pulse\Livewire\Card;

class PageViews extends Card
{
    public ?string $timeline = '24h';

    public function render()
    {
        $timeline = $this->timeline;

        $viewsData = [
            'Total' => [
                '24h'   => PageView::where('date', today())->sum('views'),
                '7d'    => PageView::where('date', '>', now()->subWeek())->sum('views'),
                'Total' => PageView::sum('views'),
            ],
            'Home' => [
                '24h'   => PageView::wherePage('home')->where('date', today())->first()?->views,
                '7d'    => PageView::wherePage('home')->where('date', '>', now()->subWeek())->sum('views'),
                'Total' => PageView::wherePage('home')->sum('views'),
            ],
            'Try' => [
                '24h'   => PageView::wherePage('try')->where('date', today())->first()?->views,
                '7d'    => PageView::wherePage('try')->where('date', '>', now()->subWeek())->sum('views'),
                'Total' => PageView::wherePage('try')->sum('views'),
            ],
            'Pricing' => [
                '24h'   => PageView::wherePage('pricing')->where('date', today())->first()?->views,
                '7d'    => PageView::wherePage('pricing')->where('date', '>', now()->subWeek())->sum('views'),
                'Total' => PageView::wherePage('pricing')->sum('views'),
            ],
            'Privacy policy' => [
                '24h'   => PageView::wherePage('privacy-policy')->where('date', today())->first()?->views,
                '7d'    => PageView::wherePage('privacy-policy')->where('date', '>', now()->subWeek())->sum('views'),
                'Total' => PageView::wherePage('privacy-policy')->sum('views'),
            ],
            'ToS' => [
                '24h'   => PageView::wherePage('terms-of-service')->where('date', today())->first()?->views,
                '7d'    => PageView::wherePage('terms-of-service')->where('date', '>', now()->subWeek())->sum('views'),
                'Total' => PageView::wherePage('terms-of-service')->sum('views'),
            ],
        ];

        return view('livewire.page-views', compact('viewsData', 'timeline'));
    }
}
