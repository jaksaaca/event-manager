<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TopStats extends BaseWidget
{
    protected static bool $isDiscovered = false;
    protected int|string|array $columnSpan = ['lg' => 1];

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', (string) User::count())
                ->description('Total user')
                ->icon('heroicon-o-user-group'),

            Stat::make('Total Events', (string) Event::count())
                ->description('Total event')
                ->icon('heroicon-o-calendar-days'),
        ];
    }
}
