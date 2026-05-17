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
        .section-break { page-break-before: always; margin-top: 20px; padding-top: 10px; border-top: 2px solid #e5e7eb; }
        .section-break:first-child { page-break-before: auto; border-top: none; margin-top: 0; padding-top: 0; }
        .section-break h3 { font-size: 14px; color: #166534; margin: 0 0 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SEIMS Analytics Report</h1>
        <p>Science Equipment Inventory Management System</p>
        <p>Generated: {{ $generated_at }}</p>
        @if(!empty($date_from) || !empty($date_to))
        <p>Period: {{ $date_from ? \Carbon\Carbon::parse($date_from)->format('M d, Y') : '—' }} — {{ $date_to ? \Carbon\Carbon::parse($date_to)->format('M d, Y') : '—' }}</p>
        @endif
    </div>

    <div class="report-type">
        <h2>{{ $report_title }}</h2>
        <p>{{ $report_description }}</p>
    </div>

    @if($type === 'all' && !empty($sections))
        @foreach($sections as $sectionType => $sectionData)
        <div class="section-break">
            <h3>{{ $section_labels[$sectionType] ?? ucfirst(str_replace('_', ' ', $sectionType)) }}</h3>
            @include('analytics.partials.report-pdf-section', ['type' => $sectionType, 'data' => $sectionData])
        </div>
        @endforeach
    @else
        @include('analytics.partials.report-pdf-section', ['type' => $type, 'data' => $data])
    @endif


    <div class="footer">
        SEIMS - Science Equipment Inventory Management System | Confidential Report | Page 1
    </div>
</body>
</html>
