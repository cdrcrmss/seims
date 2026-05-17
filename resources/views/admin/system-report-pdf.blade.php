<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1f2937; }
        .header { background: #2563eb; color: #fff; padding: 14px 20px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: bold; }
        .header p { font-size: 9px; opacity: .9; margin-top: 2px; }
        .meta { padding: 0 20px 12px; font-size: 9px; color: #6b7280; }
        .meta span { font-weight: 600; color: #374151; }
        .desc { padding: 0 20px 12px; font-size: 9px; color: #4b5563; }
        table { width: calc(100% - 40px); border-collapse: collapse; margin: 0 20px 16px; }
        th { background: #eff6ff; color: #1e40af; font-size: 8px; font-weight: 700; text-transform: uppercase;
             padding: 6px 8px; text-align: left; border-bottom: 2px solid #2563eb; }
        td { padding: 5px 8px; border-bottom: 1px solid #f3f4f6; font-size: 9px; }
        tr:nth-child(even) td { background: #f8fafc; }
        .footer { margin: 12px 20px 0; font-size: 8px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 6px; }
        .empty { text-align: center; padding: 30px; color: #9ca3af; font-size: 11px; }
        .note { padding: 0 20px 10px; font-size: 8px; color: #6b7280; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SEIMS – {{ $report_title }}</h1>
        <p>Generated: {{ $generated }}</p>
    </div>

    <div class="meta">
        @if($type !== 'item_categories')
        <div>Period: <span>{{ $date_from }} to {{ $date_to }}</span></div>
        @else
        <div>Snapshot: <span>Current inventory</span></div>
        @endif
    </div>

    @if($report_description)
    <div class="desc">{{ $report_description }}</div>
    @endif

    @if($type === 'top_borrowers')
        @if(empty($rows))
            <div class="empty">No borrowing activity in the selected period.</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Borrowings</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['role'] }}</td>
                    <td>{{ $row['count'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    @elseif($type === 'item_categories')
        @if(empty($rows))
            <div class="empty">No items in inventory.</div>
        @else
        <p class="note">Category counts reflect all items currently in inventory.</p>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category</th>
                    <th>Item Count</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach($rows as $category => $count)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $category }}</td>
                    <td>{{ $count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    @elseif($type === 'most_borrowed_items')
        @if(empty($rows))
            <div class="empty">No borrowings in the selected period.</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Borrow Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['category'] }}</td>
                    <td>{{ $row['count'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

    @elseif($type === 'borrowing_trend')
        @if(empty($rows))
            <div class="empty">No borrowing activity in the selected period.</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Borrowings</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td>{{ $row['count'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    @endif

    <div class="footer">SEIMS – Science Equipment Inventory Management System</div>
</body>
</html>
