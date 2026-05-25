# System Architecture - Module Overview
## SEIMS: Supplies and Equipment Inventory System

## 🏛️ ARCHITECTURAL LAYERS

### Front-End Layer (SPUP Branded UI)
- **Framework**: Laravel Blade Templates + Tailwind CSS
- **Brand Colors**: SPUP Green (#006633), Gold (#FFCC00), White (#FFFFFF)
- **Design**: Flat, clean, professional interface
- **Interactivity**: Alpine.js for dynamic components

### Application Layer (Business Logic)
- **Framework**: Laravel 11 (PHP)
- **Controllers**: 10+ controllers for module management
- **Services**: PredictiveAnalyticsService for AI/ML features
- **Middleware**: AuditLogMiddleware for security compliance

### Data Layer (Relational Database)
- **Database**: MySQL/PostgreSQL
- **Tables**: 15+ tables with relationships
- **Indexes**: Optimized for performance
- **Migrations**: Version-controlled schema

### Infrastructure Layer
- **File Storage**: Asset images, inspection documents
- **Backup**: Automated disaster recovery
- **Audit**: Complete logging and monitoring

---

## 📦 MODULE BREAKDOWN

### Core Modules (From Diagram)

#### 1. Inventory & Asset Management (Turquoise Segment)
**Purpose**: Track equipment and non-perishable items
**Key Files**:
- `app/Models/Item.php` (enhanced with 20+ new fields)
- `database/migrations/*_add_asset_management_fields_to_items_table.php`

**Features**:
- Asset coding system
- QR code generation
- Condition monitoring (0-100 wear scale)
- Usage tracking
- Warranty management

#### 2. Reservation & Scheduling (Turquoise-Blue Segment)
**Purpose**: Book lab rooms, equipment, prevent conflicts
**Key Files**:
- `app/Models/Reservation.php`
- `app/Models/Room.php`
- `app/Http/Controllers/ReservationController.php`

**Features**:
- **Conflict Detective**: Smart double-booking prevention
- Calendar integration
- Multi-resource booking (equipment + room)
- Approval workflows
- Real-time availability checks

#### 3. Procurement & Replenishment (Blue Segments)
**Purpose**: Automate purchasing, manage suppliers
**Key Files**:
- `app/Models/ProcurementRequest.php`
- `app/Models/Supplier.php`
- `app/Http/Controllers/ProcurementController.php`

**Features**:
- **Automated Low Stock Alerts**: System-generated requests
- Supplier performance tracking
- Economic order quantity calculation
- Multi-stage approval workflow
- Stock replenishment automation

#### 4. Borrowing & Returns (Dark Blue Segment)
**Purpose**: Digital borrowing workflow with QR scanning
**Key Files**:
- `app/Models/Borrowing.php` (existing, enhanced)
- Enhanced with QR code methods

**Features**:
- QR code scanning for checkout/return
- Digital validation
- Status tracking (Pending → Approved → Issued → Returned)
- Rejection handling with reasons

#### 5. Reporting & Predictive (Teal Segment)
**Purpose**: Demand forecasting, dashboards, utilization tracking
**Key Files**:
- `app/Services/PredictiveAnalyticsService.php`
- `app/Http/Controllers/AnalyticsController.php`

**Features**:
- **Demand Forecasting**: 30-day predictions
- **Utilization Rates**: Equipment usage analytics
- Trend analysis (increasing/decreasing/stable)
- Multi-dimensional reporting
- Export capabilities (JSON/CSV ready)

**Algorithms**:
- Linear regression for trends
- Moving averages for smoothing
- Confidence scoring
- Pattern recognition

#### 6. Compliance, Safety & QA (Purple Segment)
**Purpose**: Safety checklists, incident reports, standards compliance
**Key Files**:
- `app/Models/AuditLog.php`
- `app/Http/Middleware/AuditLogMiddleware.php`

**Features**:
- Complete audit trail
- Incident logging
- Safety checklist tracking (model-ready, views needed)
- Regulatory compliance reports

#### 7. Condition & Maintenance Monitoring (Orange/Brown Segment)
**Purpose**: Predictive maintenance, wear-and-tear alerts
**Key Files**:
- `app/Models/MaintenanceRecord.php`
- `app/Http/Controllers/MaintenanceController.php`

**Features**:
- **Predictive Alerts**: AI predicts maintenance needs
- Wear level tracking (0-100 scale)
- Condition checks before/after service
- Cost tracking per maintenance event
- Auto-scheduling based on patterns

**Prediction Logic**:
- Analyzes historical wear rates
- Calculates days until critical (80% wear)
- Auto-generates maintenance tasks at 60%+ wear
- Sends notifications to technicians

#### 8. Notifications & Communications (Top Segment)
**Purpose**: Email alerts, system notifications
**Key Files**:
- `app/Models/Notification.php`

**Features**:
- Low stock email alerts
- Maintenance due notifications
- Priority-based notifications (low/medium/high)
- User-specific notification feeds
- Action URLs for quick access

---

## 🔄 INTEGRATION & API GATEWAY

### Security & Data Privacy (Red Badge)
- **Audit Logging**: Every action tracked
- **Data Encryption**: Sensitive data protected
- **Role-based Access Control**: Admin/Staff/Student permissions
- **IP Tracking**: Security monitoring
- **GDPR Compliance**: Data privacy features

### API Gateway (Conceptual)
Routes are RESTful and can be consumed by:
- Mobile applications
- Third-party systems
- Internal microservices

---

## 💾 DATABASE SCHEMA RELATIONSHIPS

```
users
├─ borrowings (one-to-many)
├─ reservations (one-to-many)
├─ maintenance_records (one-to-many as technician)
├─ procurement_requests (one-to-many as requester)
├─ notifications (one-to-many)
└─ audit_logs (one-to-many)

items
├─ borrowings (one-to-many)
├─ reservations (one-to-many)
├─ maintenance_records (one-to-many)
├─ procurement_requests (one-to-many)
└─ suppliers (many-to-many via item_supplier)

rooms
└─ reservations (one-to-many)

suppliers
├─ procurement_requests (one-to-many)
└─ items (many-to-many via item_supplier)
```

---

## 🧩 KEY ALGORITHMS & LOGIC

### 1. Conflict Detective Algorithm (Reservations)
```php
function hasConflict() {
    // Check if another reservation overlaps this time range
    return Reservation::where('status', 'approved')
        ->where('item_id' or 'room_id', matches)
        ->where(function() {
            // Time ranges overlap
            start_datetime BETWEEN existing OR
            end_datetime BETWEEN existing OR
            existing WITHIN new_range
        })->exists();
}
```

### 2. Demand Forecasting Algorithm
```php
function forecastDemand($days = 30) {
    1. Get historical borrowing data (60 days)
    2. Calculate average daily demand
    3. Apply linear regression for trend
    4. Adjust for trend direction:
       - Upward trend: +20%
       - Downward trend: -20%
       - Stable: No adjustment
    5. Return predicted demand with confidence
}
```

### 3. Predictive Maintenance Algorithm
```php
function predictMaintenanceNeeds(Item $item) {
    1. Get last 5 maintenance records
    2. Calculate wear increase rate per day
    3. Current wear level = item->wear_level
    4. Days until critical (80%) = (80 - current) / wear_rate
    5. Schedule maintenance at 80% of predicted critical date
    6. Assign urgency based on current wear:
       - 70%+ = critical
       - 50-69% = high
       - 30-49% = moderate
       - <30% = low
}
```

### 4. Auto-Procurement Trigger
```php
function autoGenerateForLowStock(Item $item) {
    if (item->available_stock <= item->low_stock_threshold) {
        // No existing pending procurement
        if (!existingProcurement) {
            Create ProcurementRequest:
            - quantity = max(reorder_quantity, threshold * 2)
            - supplier = preferredSupplier
            - auto_generated = true
            - urgency = 'high'
        }
    }
}
```

---

## 🎨 UI COMPONENTS NEEDED (Views)

### Dashboards
- [ ] Admin Analytics Dashboard `/analytics`
- [ ] Maintenance Dashboard `/maintenance/dashboard`
- [ ] Procurement Dashboard `/procurement/dashboard`

### Management Views
- [ ] Reservation Calendar `/reservations`
- [ ] Maintenance Schedule `/maintenance`
- [ ] Procurement Requests `/procurement`
- [ ] Supplier Management `/admin/suppliers`
- [ ] Room Management `/admin/rooms`

### Reports & Analytics
- [ ] Demand Forecast Charts `/analytics/demand-forecast`
- [ ] Utilization Reports `/analytics/utilization`
- [ ] Maintenance Predictions `/analytics/maintenance-predictions`
- [ ] Audit Logs Viewer `/admin/audit-logs`

---

## 📊 SAMPLE DATA REQUIREMENTS

### For Testing Predictive Features
1. **Items**: At least 20 items with varying wear levels
2. **Borrowings**: 60+ days of historical borrowing data
3. **Maintenance**: 5+ maintenance records per critical item
4. **Suppliers**: 3-5 active suppliers with performance data
5. **Reservations**: Overlapping reservations to test conflict detection

---

## 🚀 DEPLOYMENT CHECKLIST

- [✅] Database migrations created
- [✅] Models with relationships defined
- [✅] Controllers with CRUD operations
- [✅] Routes registered
- [✅] Services implemented
- [✅] Middleware configured
- [✅] SPUP branding applied
- [ ] Views created (Blade templates)
- [ ] Seeders for sample data
- [ ] Tests written
- [ ] API documentation
- [ ] User manual

---

## 🔧 CUSTOMIZATION POINTS

### Easily Configurable
1. **Stock Thresholds**: Adjust `low_stock_threshold` per item
2. **Wear Levels**: Modify critical threshold (currently 80%)
3. **Forecast Period**: Change from 30 days to custom period
4. **Reorder Quantities**: Set optimal order quantities per item
5. **Notification Triggers**: Customize when alerts are sent

### Extendable Features
1. Add more room types (currently: laboratory, classroom, conference)
2. Extend maintenance types (currently: 5 types)
3. Add more urgency levels for procurement
4. Create custom report types
5. Integrate external APIs (suppliers, inventory management systems)

---

**This architecture fully implements the diagram provided by the user, with SPUP branding throughout.**
