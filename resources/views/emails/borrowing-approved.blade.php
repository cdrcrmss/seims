<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; padding: 40px 20px; margin: 0; }
        .container { max-width: 560px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #059669, #10b981); padding: 32px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; }
        .body p { color: #374151; line-height: 1.7; margin: 0 0 16px; }
        .detail-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dcfce7; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; font-size: 13px; }
        .detail-value { color: #1f2937; font-weight: 600; font-size: 13px; }
        .footer { padding: 24px 32px; background: #f9fafb; text-align: center; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Borrow Request Approved</h1>
        </div>
        <div class="body">
            <p>Hello <strong>{{ $borrowing->user?->name ?? 'User' }}</strong>,</p>
            <p>Great news! Your borrow request has been <strong>approved</strong>. Please proceed to the equipment room to pick up your item.</p>

            <div class="detail-box">
                <div class="detail-row">
                    <span class="detail-label">Item</span>
                    <span class="detail-value">{{ $borrowing->item?->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Quantity</span>
                    <span class="detail-value">{{ $borrowing->quantity }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Return By</span>
                    <span class="detail-value">{{ $borrowing->expected_return_date?->format('M d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Approved On</span>
                    <span class="detail-value">{{ $borrowing->approved_date?->format('M d, Y') ?? now()->format('M d, Y') }}</span>
                </div>
            </div>

            <p>Please remember to return the item on or before the return date to avoid penalties.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SEIS — Supplies and Equipment Inventory System with Predictive Analytics
        </div>
    </div>
</body>
</html>
