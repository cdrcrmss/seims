# SEIMS - Quick Reference Guide
## API Routes & Features

---

## 🔐 AUTHENTICATION & USER MANAGEMENT

### Authentication Routes (via auth.php)
- `GET /login` - Login page
- `POST /login` - Authenticate user
- `POST /logout` - Logout user
- `GET /register` - Registration page
- `POST /register` - Create new user account

### Profile Management
- `GET /profile` - View profile
- `PUT /profile` - Update profile
- `PUT /profile/password` - Change password

---

## 📊 DASHBOARD & ANALYTICS

### Main Dashboard
- `GET /dashboard` - Role-based dashboard (Admin/Staff/Student)

### Analytics Module (Staff/Admin Only)
- `GET /analytics` - Main analytics dashboard
- `GET /analytics/demand-forecast` - 30-day demand predictions
- `GET /analytics/utilization` - Equipment utilization rates
- `GET /analytics/maintenance-predictions` - Predictive maintenance schedule
- `GET /analytics/procurement` - Procurement pattern analysis
- `GET /analytics/items/{item}` - Detailed item analytics (JSON API)
- `POST /analytics/export` - Export reports (JSON/CSV)

**Analytics Features:**
- Real-time demand forecasting
- Trend analysis (increasing/decreasing/stable)
- Confidence scoring (high/medium/low)
- Equipment utilization percentages
- Predictive maintenance scheduling
- Procurement optimization recommendations

---

## 📦 INVENTORY & ASSET MANAGEMENT

### Item Management (Staff/Admin)
- `GET /staff/items` - List all items
- `GET /staff/items/create` - Create item form
- `POST /staff/items` - Store new item
- `GET /staff/items/{item}/edit` - Edit item form
- `PUT /staff/items/{item}` - Update item
- `DELETE /staff/items/{item}` - Delete item

### Enhanced Item Features
- **QR Code Generation**: Auto-generated on item creation
- **Wear Level Tracking**: 0-100 scale condition monitoring
- **Low Stock Detection**: Automatic alerts when stock ≤ threshold
- **Utilization Calculation**: Real-time usage statistics
- **Demand Prediction**: Forecasts future requirements

---

## 🔄 BORROWING & RETURNS

### Student Borrowing
- `POST /student/borrow` - Submit borrowing request
- `GET /student/borrowings` - View own borrowings
- `DELETE /student/borrowings/{borrowing}/cancel` - Cancel request

### Staff Borrowing Management
- `GET /staff/borrowings` - View all borrowings
- `PATCH /staff/borrowings/{borrowing}/approve` - Approve request
- `PATCH /staff/borrowings/{borrowing}/reject` - Reject request
- `PATCH /staff/borrowings/{borrowing}/issue` - Issue item (QR scan)
- `PATCH /staff/borrowings/{borrowing}/return` - Process return

### Borrowing Workflow
1. Student submits request → `pending`
2. Staff approves → `approved`
3. Staff issues item (QR scan) → `issued`
4. Staff processes return → `returned`
5. (Optional) Staff rejects → `rejected` with reason

---

## 📅 RESERVATION & SCHEDULING

### Reservation Management
- `GET /reservations` - List reservations
- `GET /reservations/create` - Create reservation form
- `POST /reservations` - Submit reservation request
- `POST /reservations/check-availability` - Check availability (AJAX)
- `PATCH /reservations/{reservation}/approve` - Approve (Staff/Admin)
- `PATCH /reservations/{reservation}/cancel` - Cancel reservation

### Conflict Detective Features
- **Automatic Conflict Detection**: Prevents double-booking
- **Real-time Availability**: Checks room/equipment availability
- **Multi-resource Booking**: Reserve equipment + room simultaneously
- **Conflict Resolution Notes**: Document how conflicts were resolved

### Reservation Types
- `equipment` - Equipment only
- `room` - Lab/room only
- `both` - Equipment + Room combo

---

## 🔧 MAINTENANCE & CONDITION MONITORING

### Maintenance Management (Staff/Admin)
- `GET /maintenance` - List maintenance records
- `GET /maintenance/dashboard` - Maintenance analytics dashboard
- `GET /maintenance/create` - Create maintenance record
- `POST /maintenance` - Store maintenance record
- `PATCH /maintenance/{maintenance}/complete` - Mark as completed
- `POST /maintenance/generate-alerts` - Generate predictive alerts

### Maintenance Types
- `preventive` - Scheduled preventive maintenance
- `corrective` - Repair work
- `predictive` - AI-predicted maintenance
- `routine` - Regular checkups
- `emergency` - Urgent repairs

### Predictive Maintenance Features
- **Wear-and-Tear Tracking**: 0-100 scale
- **Automatic Alerts**: Triggered at 60%+ wear
- **Predictive Scheduling**: AI calculates next maintenance date
- **Cost Tracking**: Monitor maintenance expenses
- **Urgency Levels**: Critical (70%+), High (50-69%), Moderate (30-49%), Low (<30%)

---

## 🛒 PROCUREMENT & REPLENISHMENT

### Procurement Management (Staff/Admin)
- `GET /procurement` - List procurement requests
- `GET /procurement/dashboard` - Procurement analytics
- `GET /procurement/create` - Create procurement request
- `POST /procurement` - Submit request
- `PATCH /procurement/{procurementRequest}/approve` - Approve request
- `PATCH /procurement/{procurementRequest}/mark-ordered` - Mark as ordered
- `PATCH /procurement/{procurementRequest}/mark-received` - Mark received & update stock
- `POST /procurement/generate-low-stock-alerts` - Auto-generate requests

### Automated Procurement Features
- **Low Stock Alerts**: Auto-generate when stock ≤ threshold
- **Supplier Integration**: Track preferred suppliers
- **Economic Order Quantity**: Optimized reorder quantities
- **Delivery Tracking**: Monitor order → delivery cycle
- **Stock Auto-update**: Automatic inventory adjustment on receipt

### Procurement Workflow
1. Low stock detected → Auto-generate request (`pending`)
2. Admin approves → `approved`
3. Staff marks as ordered → `ordered`
4. Item received → `received` + stock updated

### Urgency Levels
- `critical` - Immediate need
- `high` - Urgent
- `medium` - Standard
- `low` - Can wait

---

## 👥 USER MANAGEMENT (Admin Only)

### User Administration
- `GET /admin/users` - List all users
- `GET /admin/users/create` - Create user form
- `POST /admin/users` - Store new user
- `GET /admin/users/{user}/edit` - Edit user form
- `PUT /admin/users/{user}` - Update user
- `DELETE /admin/users/{user}` - Delete user

### User Roles
- `admin` - Full system access
- `staff` - Faculty/Laboratory staff
- `student` - Limited access (borrowing, reservations)

---

## 📋 REPORTS & COMPLIANCE

### Reporting (Staff/Admin)
- `GET /staff/reports` - Staff reports
- `GET /admin/reports` - Admin reports
- `GET /admin/borrowings` - All borrowing history
- `POST /analytics/export` - Export analytics data

### Audit Logging (Automatic)
Every significant action is logged:
- User identification
- IP address & user agent
- Timestamp & action type
- Before/after values
- Module & severity level

### System Settings (Admin)
- `GET /admin/settings` - View system settings
- `PUT /admin/settings` - Update settings

---

## 🔔 NOTIFICATIONS

### Notification System
- **Low Stock Alerts**: When stock ≤ threshold
- **Maintenance Due**: 7 days before scheduled maintenance
- **Procurement Updates**: Status changes on requests
- **Reservation Confirmations**: Approval/rejection notices
- **Overdue Returns**: Automatic reminders

### Notification Priority Levels
- `high` - Critical alerts (low stock, maintenance due)
- `medium` - Standard notifications
- `low` - Informational

---

## 🎨 SPUP BRANDING REFERENCE

### Color Codes
```css
/* Primary Colors */
--spup-green: #006633;      /* Headers, primary buttons, navigation */
--spup-gold: #FFCC00;       /* Highlights, warnings, alerts */
--white: #FFFFFF;           /* Backgrounds, cards */

/* Gradations */
--spup-green-light: #cce6db;
--spup-green-dark: #003d1f;
--spup-gold-light: #fff3cc;
--spup-gold-dark: #997a00;
```

### CSS Classes Available
- `.bg-spup-green` - SPUP green background
- `.bg-spup-gold` - SPUP gold background
- `.text-spup-green` - SPUP green text
- `.text-spup-gold` - SPUP gold text
- `.border-spup-green` - SPUP green border
- `.border-spup-gold` - SPUP gold border
- `.btn-primary` - SPUP green button
- `.btn-secondary` - SPUP gold button

---

## 🔍 SEARCH & FILTERING

### Available Scopes (Model Queries)
```php
// Items
Item::lowStock()                // Items below threshold
Item::maintenanceDue()           // Maintenance due within 7 days

// Maintenance Records
MaintenanceRecord::upcoming()    // Scheduled within 30 days
MaintenanceRecord::overdue()     // Past scheduled date

// Reservations
Reservation::active()            // Active reservations

// Procurement Requests
ProcurementRequest::pending()    // Pending approval
ProcurementRequest::autoGenerated()  // System-generated
```

---

## 📊 PREDICTIVE ANALYTICS ENDPOINTS (API)

### Demand Forecasting
```
GET /analytics/items/{item}
Response: {
    "forecast": {
        "average_daily_demand": 2.5,
        "predicted_demand": 75,
        "confidence": "high",
        "trend": "increasing",
        "historical_data_points": 45
    }
}
```

### Utilization Rate
```
Response: {
    "utilization": {
        "utilization_rate": 73.2,
        "total_hours_used": 527.4,
        "total_possible_hours": 720,
        "status": "moderate"
    }
}
```

### Maintenance Prediction
```
Response: {
    "maintenance_prediction": {
        "next_maintenance_date": "2026-03-15",
        "current_wear_level": 65,
        "average_wear_rate": 0.8,
        "days_until_critical": 18,
        "urgency": "high",
        "confidence": "high"
    }
}
```

---

## 🚀 QUICK START COMMANDS

### Development
```bash
# Start server
php artisan serve

# Run migrations
php artisan migrate

# Refresh database
php artisan migrate:fresh

# Seed data
php artisan db:seed

# Clear caches
php artisan optimize:clear
```

### Asset Compilation
```bash
# Development mode
npm run dev

# Production build
npm run build

# Watch for changes
npm run watch
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=ReservationTest
```

---

## 🔗 KEY MODEL Relationships

### Item Relationships
```php
$item->borrowings           // All borrowing records
$item->reservations         // All reservations
$item->maintenanceRecords   // Maintenance history
$item->procurementRequests  // Purchase requests
$item->suppliers            // Associated suppliers
$item->preferredSupplier()  // Primary supplier
```

### User Relationships
```php
$user->borrowings          // User's borrowings
$user->reservations        // User's reservations
$user->procurementRequests // Requests made by user
$user->notifications       // User's notifications
$user->auditLogs          // Actions performed
```

---

## ⚙️ CONFIGURATION OPTIONS

### Environment Variables (.env)
```env
# Analytics
ANALYTICS_FORECAST_DAYS=30
ANALYTICS_CONFIDENCE_THRESHOLD=5

# Maintenance
MAINTENANCE_CRITICAL_THRESHOLD=80
MAINTENANCE_WARNING_THRESHOLD=60

# Procurement
LOW_STOCK_AUTO_GENERATE=true
DEFAULT_REORDER_QUANTITY=10
DEFAULT_LOW_STOCK_THRESHOLD=5

# Notifications
NOTIFICATION_EMAIL_ENABLED=true
NOTIFICATION_SMS_ENABLED=false
```

---

## 📧 NOTIFICATION METHODS

### Creating Notifications Programmatically
```php
// Low stock alert
Notification::createLowStockAlert($item, $user);

// Maintenance alert
Notification::createMaintenanceAlert($item, $maintenance, $user);

// Custom notification
Notification::create([
    'user_id' => $userId,
    'type' => 'custom',
    'title' => 'Title',
    'message' => 'Message',
    'priority' => 'high',
    'action_url' => route('some.route'),
]);
```

---

## 🏷️ ITEM STATUS VALUES

- `available` - Ready for borrowing/use
- `in_use` - Currently borrowed
- `maintenance` - Under maintenance
- `retired` - No longer in service
- `damaged` - Needs repair

---

## 📦 RESERVATION STATUS VALUES

- `pending` - Awaiting approval
- `approved` - Approved, not yet active
- `rejected` - Request denied
- `cancelled` - Cancelled by user
- `completed` - Reservation period ended

---

## 🔧 MAINTENANCE STATUS VALUES

- `scheduled` - Planned maintenance
- `in_progress` - Currently being serviced
- `completed` - Service finished
- `cancelled` - Maintenance cancelled

---

## 🛒 PROCUREMENT STATUS VALUES

- `pending` - Awaiting approval
- `approved` - Approved, ready to order
- `rejected` - Request denied
- `ordered` - Purchase order sent
- `received` - Items received (stock updated)
- `cancelled` - Request cancelled

---

**For detailed implementation instructions, see [IMPLEMENTATION_GUIDE.md](IMPLEMENTATION_GUIDE.md)**
**For architectural details, see [ARCHITECTURE_DOCUMENTATION.md](ARCHITECTURE_DOCUMENTATION.md)**
