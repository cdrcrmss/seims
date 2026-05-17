@if($type === 'comprehensive')
    @if(isset($data['inventory_summary']))
    <div class="section">
        <div class="section-title">Inventory Summary</div>
        <table>
            <tr><th>Metric</th><th>Value</th></tr>
            <tr><td>Total Items</td><td>{{ $data['inventory_summary']['total_items'] }}</td></tr>
            <tr><td>Low Stock Items</td><td>{{ $data['inventory_summary']['low_stock_items'] }}</td></tr>
        </table>
    </div>
    @endif
    @if(isset($data['borrowing_summary']))
    <div class="section">
        <div class="section-title">Borrowing Summary</div>
        <table>
            <tr><th>Metric</th><th>Value</th></tr>
            <tr><td>Total Borrowings</td><td>{{ $data['borrowing_summary']['total_borrowings'] }}</td></tr>
            <tr><td>Active Borrowings</td><td>{{ $data['borrowing_summary']['active_borrowings'] }}</td></tr>
            <tr><td>Overdue Returns</td><td>{{ $data['borrowing_summary']['overdue_returns'] }}</td></tr>
        </table>
    </div>
    @endif
    @if(isset($data['maintenance_summary']))
    <div class="section">
        <div class="section-title">Maintenance Summary</div>
        <table>
            <tr><th>Metric</th><th>Value</th></tr>
            <tr><td>Total Maintenance Records</td><td>{{ $data['maintenance_summary']['total_maintenance'] }}</td></tr>
            <tr><td>Upcoming Maintenance</td><td>{{ $data['maintenance_summary']['upcoming'] }}</td></tr>
            <tr><td>Overdue Maintenance</td><td>{{ $data['maintenance_summary']['overdue'] }}</td></tr>
        </table>
    </div>
    @endif

@elseif($type === 'demand_forecast' && isset($data['items']))
    <div class="section">
        <div class="section-title">Demand Forecast (Next 30 Days)</div>
        <table>
            <tr>
                <th>Item Name</th><th>Category</th><th>Current Stock</th>
                <th>Predicted Demand</th><th>Confidence</th>
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
                <th>Item Name</th><th>Category</th><th>Utilization Rate</th>
                <th>Total Hours Used</th><th>Status</th>
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
            <tr><th>Metric</th><th>Value</th></tr>
            <tr><td>Total Records</td><td>{{ $data['total_records'] ?? 0 }}</td></tr>
            <tr><td>Upcoming</td><td>{{ $data['upcoming'] ?? 0 }}</td></tr>
            <tr><td>Overdue</td><td>{{ $data['overdue'] ?? 0 }}</td></tr>
            <tr><td>Completed This Year</td><td>{{ $data['completed_this_year'] ?? 0 }}</td></tr>
        </table>
    </div>
    @if(isset($data['critical_items']) && count($data['critical_items']) > 0)
    <div class="section">
        <div class="section-title">Critical Items (Wear ≥ 70%)</div>
        <table>
            <tr><th>Item Name</th><th>Wear Level</th><th>Category</th></tr>
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
@endif
