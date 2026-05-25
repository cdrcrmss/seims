@component('mail::message')
# Maintenance Alert

Hello {{ $recipient->name }},

An item in the inventory requires scheduled maintenance.

**Item Details:**
- **Name:** {{ $item->name }}
- **Category:** {{ ucfirst($item->category) }}
- **Asset Code:** {{ $item->asset_code ?? 'N/A' }}
- **Wear Level:** {{ $item->wear_level ?? 0 }}%

**Maintenance Details:**
- **Type:** {{ ucfirst(str_replace('_', ' ', $maintenance->type ?? 'scheduled')) }}
- **Scheduled Date:** {{ $maintenance->scheduled_date ? $maintenance->scheduled_date->format('F d, Y') : 'Not set' }}
- **Priority:** {{ ucfirst($maintenance->priority ?? 'normal') }}
@if($maintenance->notes)
- **Notes:** {{ $maintenance->notes }}
@endif

@component('mail::button', ['url' => $actionUrl])
View Item Details
@endcomponent

Please ensure the maintenance is performed as scheduled to keep equipment in optimal condition.

Thanks,<br>
{{ config('app.name') }}

---
*This is an automated notification from the SEIS (Supplies and Equipment Inventory System with Predictive Analytics).*
@endcomponent
