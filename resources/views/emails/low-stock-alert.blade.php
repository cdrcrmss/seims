@component('mail::message')
# Low Stock Alert

Hello {{ $recipient->name }},

An item in the inventory is running low on stock and requires attention.

**Item Details:**
- **Name:** {{ $item->name }}
- **Category:** {{ ucfirst($item->category) }}
- **Available Stock:** {{ $item->available_stock }}
- **Low Stock Threshold:** {{ $item->low_stock_threshold ?? 5 }}
- **Asset Code:** {{ $item->asset_code ?? 'N/A' }}

@component('mail::button', ['url' => $actionUrl])
View Item Details
@endcomponent

Please restock this item as soon as possible to avoid disruptions in supply.

Thanks,<br>
{{ config('app.name') }}

---
*This is an automated notification from the SEIS (Supplies and Equipment Inventory System with Predictive Analytics).*
@endcomponent
