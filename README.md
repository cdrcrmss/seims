# SEIMS - Laboratory Equipment Inventory Management System

A modern, feature-rich laboratory equipment inventory and borrowing management system built with Laravel and Tailwind CSS.

## Project Description

SEIMS is a comprehensive inventory management system designed for educational institutions to track laboratory equipment, manage borrowing requests, and streamline the equipment lending process. The platform provides role-based access for administrators, staff, and students.

## Features

### User Management
- **Role-Based Access Control** - Admin, Staff, and Student roles with different permissions
- **User Authentication** - Secure login and registration system
- **Profile Management** - Edit personal information and change passwords

### Inventory Management
- **Equipment Catalog** - Add, edit, and delete laboratory equipment
- **Stock Tracking** - Monitor total and available stock levels
- **Category Organization** - Organize items by categories (Electronics, Mechanical, Chemical, etc.)
- **Image Support** - Upload and display equipment images

### Borrowing System
- **Borrow Requests** - Students can request to borrow equipment
- **Approval Workflow** - Staff/Admin can approve or reject requests
- **Issue & Return Tracking** - Track equipment from issue to return
- **Status Management** - Pending, Approved, Issued, Returned, Rejected statuses

### Dashboard & Analytics
- **Admin Dashboard** - Overview of system statistics and pending requests
- **Staff Dashboard** - Equipment and borrowing management interface
- **Student Dashboard** - View available equipment and borrowing history

## Technology Stack

- **Backend:** Laravel 11 (PHP 8.2+)
- **Frontend:** Blade Templates, Tailwind CSS, Alpine.js
- **Database:** MySQL/SQLite
- **Build Tools:** Vite, npm

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL or SQLite

### Setup Instructions

```bash
# Clone the repository
git clone <repository-url>
cd SEIMS

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env file

# Run migrations
php artisan migrate

# Create storage link
php artisan storage:link

# Build assets
npm run build

# Start the development server
php artisan serve
```

## Usage

1. **Admin Account** - Manage users, view reports, configure system settings
2. **Staff Account** - Manage equipment inventory, process borrowing requests
3. **Student Account** - Browse equipment, submit borrow requests, track borrowing history

## Credits

**Developer:** Cedric Ramos

**Code generated with ChatGPT assistance**

---

Made with Laravel and Tailwind CSS
