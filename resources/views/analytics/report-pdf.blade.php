<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SEIMS Analytics Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.5;
            margin: 0;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            font-size: 22px;
            color: #16a34a;
            margin: 0 0 5px 0;
        }
        .header p {
            color: #6b7280;
            margin: 0;
            font-size: 11px;
        }
        .report-type {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
        .report-type h2 {
            font-size: 14px;
            color: #166534;
            margin: 0 0 3px 0;
        }
        .report-type p {
            color: #6b7280;
            margin: 0;
            font-size: 10px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
        }
        table td {
            border: 1px solid #e5e7eb;
            padding: 7px 10px;
            font-size: 11px;
            color: #1f2937;
        }
        table tr:nth-child(even) {
            background: #f9fafb;
        }
        .stat-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .stat-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #16a34a;
        }
        .stat-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            margin-top: 3px;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 30px;
            right: 30px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SEIMS Analytics Report</h1>
        <p>Science Equipment Inventory Management System</p>
        <p>Generated: {{ $generated_at }}</p>
    </div>

    <div class="report-type">
        <h2>{{ $report_title }}</h2>
        <p>{{ $report_description }}</p>
    </div>

    @if($type === 'comprehensive')
        {{-- Summary Stats --}}
        @if(isset($data['inventory_summary']))
        <div class="section">
            <div class="section-title">Inventory Summary</div>
            <table>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Total Items</td>
                    <td>{{ $data['inventory_summary']['total_items'] }}</td>
                </tr>
                <tr>
                    <td>Low Stock Items</td>
                    <td>{{ $data['inventory_summary']['low_stock_items'] }}</td>
                </tr>
                <tr>
                    <td>Total Inventory Value</td>
                    <td>&#8369;{{ number_format($data['inventory_summary']['total_value'], 2) }}</td>
                </tr>
            </table>
        </div>
        @endif

        @if(isset($data['borrowing_summary']))
        <div class="section">
            <div class="section-title">Borrowing Summary</div>
            <table>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Total Borrowings</td>
                    <td>{{ $data['borrowing_summary']['total_borrowings'] }}</td>
                </tr>
                <tr>
                    <td>Active Borrowings</td>
                    <td>{{ $data['borrowing_summary']['active_borrowings'] }}</td>
                </tr>
                <tr>
                    <td>Overdue Returns</td>
                    <td>{{ $data['borrowing_summary']['overdue_returns'] }}</td>
                </tr>
            </table>
        </div>
        @endif

        @if(isset($data['maintenance_summary']))
        <div class="section">
            <div class="section-title">Maintenance Summary</div>
            <table>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Total Maintenance Records</td>
                    <td>{{ $data['maintenance_summary']['total_maintenance'] }}</td>
                </tr>
                <tr>
                    <td>Upcoming Maintenance</td>
                    <td>{{ $data['maintenance_summary']['upcoming'] }}</td>
                </tr>
                <tr>
                    <td>Overdue Maintenance</td>
                    <td>{{ $data['maintenance_summary']['overdue'] }}</td>
                </tr>
                <tr>
                    <td>Maintenance Costs (This Year)</td>
                    <td>&#8369;{{ number_format($data['maintenance_summary']['total_costs'], 2) }}</td>
                </tr>
            </table>
        </div>
        @endif

    @elseif($type === 'demand_forecast' && isset($data['items']))
        <div class="section">
            <div class="section-title">Demand Forecast (Next 30 Days)</div>
            <table>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Predicted Demand</th>
                    <th>Confidence</th>
                </tr>
                @foreach($data['items'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['category'] }}</td>
                    <td>{{ $item['current_stock'] }}</td>
                    <td>{{ $item['forecast']['predicted_demand'] ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ ($item['forecast']['confidence'] ?? '') === 'high' ? 'badge-green' : (($item['forecast']['confidence'] ?? '') === 'medium' ? 'badge-yellow' : 'badge-red') }}">
                            {{ ucfirst($item['forecast']['confidence'] ?? 'N/A') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>

    @elseif($type === 'utilization' && isset($data['items']))
        <div class="section">
            <div class="section-title">Equipment Utilization (Last 30 Days)</div>
            <table>
                <tr>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Utilization Rate</th>
                    <th>Total Hours Used</th>
                    <th>Status</th>
                </tr>
                @foreach($data['items'] as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['category'] }}</td>
                    <td>{{ $item['utilization']['utilization_rate'] ?? 'N/A' }}%</td>
                    <td>{{ $item['utilization']['total_hours_used'] ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ ($item['utilization']['status'] ?? '') === 'high' ? 'badge-red' : (($item['utilization']['status'] ?? '') === 'moderate' ? 'badge-yellow' : 'badge-green') }}">
                            {{ ucfirst($item['utilization']['status'] ?? 'N/A') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>

    @elseif($type === 'maintenance')
        <div class="section">
            <div class="section-title">Maintenance Overview</div>
            <table>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
                <tr><td>Total Records</td><td>{{ $data['total_records'] ?? 0 }}</td></tr>
                <tr><td>Upcoming</td><td>{{ $data['upcoming'] ?? 0 }}</td></tr>
                <tr><td>Overdue</td><td>{{ $data['overdue'] ?? 0 }}</td></tr>
                <tr><td>Completed This Year</td><td>{{ $data['completed_this_year'] ?? 0 }}</td></tr>
                <tr><td>Total Costs This Year</td><td>&#8369;{{ number_format($data['total_costs_this_year'] ?? 0, 2) }}</td></tr>
            </table>
        </div>

        @if(isset($data['critical_items']) && count($data['critical_items']) > 0)
        <div class="section">
            <div class="section-title">Critical Items (Wear &ge; 70%)</div>
            <table>
                <tr>
                    <th>Item Name</th>
                    <th>Wear Level</th>
                    <th>Category</th>
                </tr>
                @foreach($data['critical_items'] as $ci)
                <tr>
                    <td>{{ $ci['name'] }}</td>
                    <td>{{ $ci['wear_level'] }}%</td>
                    <td>{{ $ci['category'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

    @elseif($type === 'procurement')
        <div class="section">
            <div class="section-title">Procurement Overview</div>
            <table>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
                <tr><td>Total Requests</td><td>{{ $data['total_requests'] ?? 0 }}</td></tr>
                <tr><td>Pending</td><td>{{ $data['pending'] ?? 0 }}</td></tr>
                <tr><td>Total Spending (This Year)</td><td>&#8369;{{ number_format($data['total_spending_this_year'] ?? 0, 2) }}</td></tr>
                <tr><td>Auto-Generated Count</td><td>{{ $data['auto_generated_count'] ?? 0 }}</td></tr>
            </table>
        </div>

        @if(isset($data['low_stock_items']) && count($data['low_stock_items']) > 0)
        <div class="section">
            <div class="section-title">Low Stock Items</div>
            <table>
                <tr>
                    <th>Item Name</th>
                    <th>Available</th>
                    <th>Threshold</th>
                    <th>Category</th>
                </tr>
                @foreach($data['low_stock_items'] as $li)
                <tr>
                    <td>{{ $li['name'] }}</td>
                    <td>{{ $li['available_stock'] }}</td>
                    <td>{{ $li['low_stock_threshold'] }}</td>
                    <td>{{ $li['category'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif
    @endif

    <div class="footer">
        SEIMS - Science Equipment Inventory Management System | Confidential Report | Page 1
    </div>
</body>
</html>
