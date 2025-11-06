<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Widgets\ChartWidget;

class EventsPerMonthChart extends ChartWidget
{
    protected static ?string $heading = 'Events per Month';
    protected static bool $isDiscovered = false;
    protected int|string|array $columnSpan = ['lg' => 1];

    protected function getData(): array
    {
        $end   = Carbon::now()->endOfMonth();
        $start = (clone $end)->subMonths(11)->startOfMonth();

        // Ambil semua event di rentang waktu itu
        $events = Event::query()
            ->whereBetween('tanggal_event', [$start, $end])
            ->get(['tanggal_event']);

        $labels = [];
        $counts = [];

        // Loop setiap bulan
        foreach (CarbonPeriod::create($start, '1 month', $end) as $monthStart) {
            $monthEnd = (clone $monthStart)->endOfMonth();
            $labels[] = $monthStart->format('M Y'); // Contoh: Nov 2025

            $counts[] = $events->filter(fn ($e) =>
                Carbon::parse($e->tanggal_event)->between($monthStart, $monthEnd)
            )->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Event',
                    'data'  => $counts,
                    'borderColor' => '#f59e0b', // Warna kuning amber (optional)
                    'fill' => false,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
