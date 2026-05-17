@if($type === 'top_borrowers')
    @if(empty($rows))
        <div class="empty">No borrowing activity in the selected period.</div>
    @else
    <table>
        <thead>
            <tr><th>#</th><th>Name</th><th>Role</th><th>Borrowings</th></tr>
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
            <tr><th>#</th><th>Category</th><th>Item Count</th></tr>
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
            <tr><th>#</th><th>Item</th><th>Category</th><th>Borrow Count</th></tr>
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
            <tr><th>Date</th><th>Borrowings</th></tr>
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
