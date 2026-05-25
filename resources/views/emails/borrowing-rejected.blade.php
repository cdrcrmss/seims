<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f4f6; padding: 40px 20px; margin: 0; }
        .container { max-width: 560px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #dc2626, #ef4444); padding: 32px; text-align: center; color: white; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; }
        .body p { color: #374151; line-height: 1.7; margin: 0 0 16px; }
        .detail-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 20px; margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #fee2e2; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; font-size: 13px; }
        .detail-value { color: #1f2937; font-weight: 600; font-size: 13px; }
        .reason-box { background: #fff7ed; border: 1px solid #fed7aa; border-radius: 12px; padding: 16px; margin: 16px 0; }
        .reason-box p { margin: 0; color: #92400e; font-size: 14px; }
        .footer { padding: 24px 32px; background: #f9fafb; text-align: center; color: #9ca3af; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Borrow Request Rejected</h1>
        </div>
        <div class="body">
            <p>Hello <strong>{{ $borrowing->user?->name ?? 'User' }}</strong>,</p>
            <p>Unfortunately, your borrow request has been <strong>rejected</strong>.</p>

            <div class="detail-box">
                <div class="detail-row">
                    <span class="detail-label">Item</span>
                    <span class="detail-value">{{ $borrowing->item?->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Quantity Requested</span>
                    <span class="detail-value">{{ $borrowing->quantity }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Requested On</span>
                    <span class="detail-value">{{ $borrowing->requested_date?->format('M d, Y') ?? $borrowing->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            @if($borrowing->rejection_reason)
                <div class="reason-box">
                    <p><strong>Reason:</strong> {{ $borrowing->rejection_reason }}</p>
                </div>
            @endif

            <p>If you have questions, please contact the equipment room staff.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} SEIS — Supplies and Equipment Inventory System with Predictive Analytics
        </div>
    </div>
</body>
</html>
