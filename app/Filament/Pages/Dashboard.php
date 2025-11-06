<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use App\Filament\Widgets\TopStats;
use App\Filament\Widgets\EventsPerMonthChart;
use App\Filament\Widgets\LatestLogins;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class, 
            TopStats::class, 
            EventsPerMonthChart::class,
            LatestLogins::class,
        ];
    }
}
