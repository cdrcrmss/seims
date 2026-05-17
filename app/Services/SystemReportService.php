<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SystemReportService
{
    public function dateRange(?string $from, ?string $to): array
    {
        $start = $from ? Carbon::parse($from)->startOfDay() : now()->startOfMonth();
        $end = $to ? Carbon::parse($to)->endOfDay() : now()->endOfDay();

        if ($start->gt($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    public function topBorrowers(?string $from, ?string $to, int $limit = 20): array
    {
        [$start, $end] = $this->dateRange($from, $to);

        return User::withCount(['borrowings' => function ($query) use ($start, $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }])
            ->whereHas('borrowings', function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end]);
            })
            ->orderByDesc('borrowings_count')
            ->limit($limit)
            ->get()
            ->map(fn ($user) => [
                'name' => $user->name,
                'count' => $user->borrowings_count,
                'role' => ucfirst($user->role),
            ])
            ->values()
            ->toArray();
    }

    public function itemsByCategory(): array
    {
        return Item::query()
            ->selectRaw("COALESCE(NULLIF(TRIM(category), ''), 'Uncategorized') as category_label, COUNT(*) as count")
            ->groupBy('category_label')
            ->orderByDesc('count')
            ->pluck('count', 'category_label')
            ->toArray();
    }

    public function mostBorrowedItems(?string $from, ?string $to, int $limit = 20): array
    {
        [$start, $end] = $this->dateRange($from, $to);

        return Borrowing::select('item_id', DB::raw('COUNT(*) as borrow_count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('item_id')
            ->orderByDesc('borrow_count')
            ->limit($limit)
            ->with('item')
            ->get()
            ->filter(fn ($b) => $b->item !== null)
            ->map(fn ($b) => [
                'name' => $b->item->name,
                'category' => $b->item->category ?? '—',
                'count' => $b->borrow_count,
            ])
            ->values()
            ->toArray();
    }

    public function borrowingTrend(?string $from, ?string $to): array
    {
        [$start, $end] = $this->dateRange($from, $to);

        $trends = [];
        $cursor = $start->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $date = $cursor->toDateString();
            $trends[] = [
                'date' => $date,
                'label' => $cursor->format('M d, Y'),
                'count' => Borrowing::whereDate('created_at', $date)->count(),
            ];
            $cursor->addDay();
        }

        return $trends;
    }

    public function reportMeta(string $type): array
    {
        $titles = [
            'top_borrowers' => 'Top Borrowers Report',
            'item_categories' => 'Item Categories Report',
            'most_borrowed_items' => 'Most Borrowed Items Report',
            'borrowing_trend' => 'Borrowing Trends Report',
        ];

        $descriptions = [
            'top_borrowers' => 'Users with the most borrowing activity in the selected period.',
            'item_categories' => 'Current inventory distribution across item categories.',
            'most_borrowed_items' => 'Equipment borrowed most frequently in the selected period.',
            'borrowing_trend' => 'Daily borrowing activity across the selected period.',
        ];

        return [
            'title' => $titles[$type] ?? 'System Report',
            'description' => $descriptions[$type] ?? '',
        ];
    }
}
