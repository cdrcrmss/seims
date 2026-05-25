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
        .section-break { page-break-before: always; margin-top: 16px; padding-top: 10px; border-top: 2px solid #dbeafe; }
        .section-break:first-of-type { page-break-before: auto; border-top: none; margin-top: 0; padding-top: 0; }
        .section-break h3 { font-size: 12px; color: #1e40af; margin: 0 0 10px 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SEIS – {{ $report_title }}</h1>
        <p>Generated: {{ $generated }}</p>
    </div>

    <div class="meta">
        @if($type === 'all' || $type !== 'item_categories')
        <div>Period: <span>{{ $date_from }} to {{ $date_to }}</span></div>
        @endif
        @if($type === 'item_categories')
        <div>Snapshot: <span>Current inventory</span></div>
        @endif
    </div>

    @if($report_description)
    <div class="desc">{{ $report_description }}</div>
    @endif

    @if($type === 'all' && !empty($sections))
        @foreach($sections as $sectionType => $sectionRows)
        <div class="section-break">
            <h3>{{ $section_labels[$sectionType] ?? ucfirst(str_replace('_', ' ', $sectionType)) }}</h3>
            @include('admin.partials.system-report-pdf-section', ['type' => $sectionType, 'rows' => $sectionRows])
        </div>
        @endforeach
    @else
        @include('admin.partials.system-report-pdf-section', ['type' => $type, 'rows' => $rows])
    @endif

    <div class="footer">SEIS – Supplies and Equipment Inventory System with Predictive Analytics</div>
</body>
</html>
