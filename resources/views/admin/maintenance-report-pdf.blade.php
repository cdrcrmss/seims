<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        .header { background: #f59e0b; color: #fff; padding: 14px 20px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p { font-size: 9px; opacity: .85; margin-top: 2px; }
        .meta { display: flex; gap: 24px; padding: 0 20px 12px; font-size: 9px; color: #6b7280; }
        .meta span { font-weight: 600; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin: 0 20px; width: calc(100% - 40px); }
        th { background: #fef3c7; color: #92400e; font-size: 8px; font-weight: 700; text-transform: uppercase;
             padding: 6px 8px; text-align: left; border-bottom: 2px solid #f59e0b; }
        td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; vertical-align: top; font-size: 9px; }
        tr:nth-child(even) td { background: #fffbeb; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 4px; font-size: 8px; font-weight: 600; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-scheduled { background: #dbeafe; color: #1e40af; }
        .badge-in_progress { background: #fef3c7; color: #92400e; }
        .badge-overdue { background: #fee2e2; color: #991b1b; }
        .footer { margin: 12px 20px 0; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 6px; }
        .empty { text-align: center; padding: 30px; color: #9ca3af; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SEIMS – Maintenance Report</h1>
        <p>Generated: {{ $generated }}</p>
    </div>

    <div class="meta">
        <div>Period: <span>{{ $date_from ?? '—' }} to {{ $date_to ?? '—' }}</span></div>
        <div>Status: <span>{{ $status && $status !== 'all' ? ucfirst(str_replace('_', ' ', $status)) : 'All' }}</span></div>
        <div>Type: <span>{{ $maint_type && $maint_type !== 'all' ? ucfirst($maint_type) : 'All' }}</span></div>
        <div>Total records: <span>{{ $records->count() }}</span></div>
    </div>

    @if($records->isEmpty())
        <div class="empty">No maintenance records match the selected filters.</div>
    @else
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Type</th>
                <th>Status</th>
                <th>Scheduled</th>
                <th>Completed</th>
                <th>Performed By</th>
                <th>Wear %</th>
                <th>Cost</th>
                <th>Next Maint.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->item->name ?? 'N/A' }}</td>
                <td>{{ ucfirst($r->maintenance_type) }}</td>
                <td>
                    <span class="badge badge-{{ $r->status }}">
                        {{ ucfirst(str_replace('_', ' ', $r->status)) }}
                    </span>
                </td>
                <td>{{ $r->scheduled_date?->format('M d, Y') }}</td>
                <td>{{ $r->completed_date?->format('M d, Y') ?? '—' }}</td>
                <td>{{ $r->technician->name ?? 'N/A' }}</td>
                <td>{{ $r->wear_level ?? '—' }}%</td>
                <td>{{ $r->cost ? '₱' . number_format($r->cost, 2) : '—' }}</td>
                <td>{{ $r->next_maintenance_date?->format('M d, Y') ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="footer">
        SEIMS – Supplies and Equipment Inventory Management System &nbsp;|&nbsp; {{ $generated }}
    </div>
</body>
</html>
