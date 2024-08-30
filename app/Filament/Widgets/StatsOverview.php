<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;
    protected function getStats(): array
    {
        $startDate = ! is_null($this->filters['startDate'] ?? null) ?
            Carbon::parse($this->filters['startDate']) :
            now()->subDay();

        $endDate = ! is_null($this->filters['endDate'] ?? null) ?
            Carbon::parse($this->filters['endDate']) :
            now();

        $pemasukkan = Transaction::incomes()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        $pengeluaran = Transaction::outcomes()->whereBetween('date_transaction', [$startDate, $endDate])->sum('amount');
        $selisih = $pemasukkan - $pengeluaran;

        //format rupiah dan penambahan Rp di depan
        $pemasukkan = "Rp " . number_format($pemasukkan, 0, ',', '.');
        $pengeluaran = "Rp " . number_format($pengeluaran, 0, ',', '.');
        $selisih = "Rp " . number_format($selisih, 0, ',', '.');


        return [
            Stat::make('Total Pemasukkan', $pemasukkan),
            Stat::make('Total Pengeluaran', $pengeluaran),
            Stat::make('Selisih', $selisih),
        ];
    }
}
