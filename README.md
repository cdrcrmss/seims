# SEIMS - Laboratory Equipment Inventory Management System

A modern, feature-rich laboratory equipment inventory and borrowing management system built with Laravel and Tailwind CSS.

## Project Description

SEIMS is a comprehensive inventory management system designed for educational institutions to track laboratory equipment, manage borrowing requests, and streamline the equipment lending process. The platform provides role-based access for administrators, staff, and students.

## Features

### Login Page
<img width="2559" height="1470" alt="image" src="https://github.com/user-attachments/assets/f446d90b-2d80-47f8-9b7d-647a71aaba88" />

### User Management
- **Role-Based Access Control** - Admin, Staff, and Student roles with different permissions
- **User Authentication** - Secure login and registration system
- **Profile Management** - Edit personal information and change passwords
  <img width="2554" height="1465" alt="image" src="https://github.com/user-attachments/assets/785df1da-4c91-4dfc-8e10-54c25e4102ca" />

### Inventory Management
- **Equipment Catalog** - Add, edit, and delete laboratory equipment
- **Stock Tracking** - Monitor total and available stock levels
- **Category Organization** - Organize items by categories (Electronics, Mechanical, Chemical, etc.)
- **Image Support** - Upload and display equipment images
  <img width="2559" height="1467" alt="image" src="https://github.com/user-attachments/assets/637ca251-39f8-424c-836f-ce2913177729" />

### Borrowing System
- **Borrow Requests** - Students can request to borrow equipment
- **Approval Workflow** - Staff/Admin can approve or reject requests
- **Issue & Return Tracking** - Track equipment from issue to return
- **Status Management** - Pending, Approved, Issued, Returned, Rejected statuses
  <img width="2559" height="1468" alt="image" src="https://github.com/user-attachments/assets/9570891c-9688-4500-8229-35d7f74cf174" />
  <img width="2559" height="1465" alt="image" src="https://github.com/user-attachments/assets/55137e3d-2999-45b8-b31a-50ab77fdb3e9" />

### Dashboard & Analytics
- **Admin Dashboard** - Overview of system statistics and pending requests
  <img width="2558" height="1469" alt="image" src="https://github.com/user-attachments/assets/7844dc35-de39-401c-b5f7-1cdaf3c6c650" />

- **Staff Dashboard** - Equipment and borrowing management interface
  <img width="2559" height="1470" alt="image" src="https://github.com/user-attachments/assets/956af8cd-33e3-4311-91d7-7af031be57bf" />

- **Student Dashboard** - View available equipment and borrowing history
  <img width="2559" height="1469" alt="image" src="https://github.com/user-attachments/assets/67cfffd1-d3f4-4672-80a2-ed39d9c8f3a7" />

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
