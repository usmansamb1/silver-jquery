# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**JoilYaseeir** is an RFID gas-station payment & service-booking platform with web admin dashboards and customer-facing service portal. The system handles wallet top-ups, service bookings, approval workflows, and RFID card management for fuel stations.

## Technology Stack

- **Backend**: Laravel 10 (PHP 8.3+), MySQL Server 8.0+
- **Frontend**: Bootstrap 5.3, jQuery 3.6, Axios, SweetAlert2, Blade Components
- **Payments**: HyperPay (credit/MADA cards), wallet system
- **Notifications**: Mail (Microsoft 365 SMTP), SMS via https://authentica.sa/
- **Authentication**: OTP via SMS & email, Spatie Laravel Permission for roles
- **Development**: Vite for asset compilation, Laravel Octane with RoadRunner

## Common Development Commands

### Server Management
```bash
# Start development server
php artisan serve --host=127.0.0.1 --port=8001

# Start with Octane (production-like performance)
php artisan octane:start --host=127.0.0.1 --port=8001

# Asset compilation
npm run dev          # Development with hot reload
npm run build        # Production build
```

### Database Operations
```bash
# Run migrations
php artisan migrate

# Fresh migration with seeders
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name
```

### Code Quality & Testing
```bash
# PHP Pint (PSR-12 formatting)
./vendor/bin/pint

# Run tests
php artisan test

# Single test
php artisan test --filter=TestClassName
```

### Cache & Optimization
```bash
# Clear all caches
php artisan optimize:clear

# Production optimization
php artisan optimize
```

## Architecture Overview

### Core Business Logic

#### User Management & Authentication
- **Multi-role system**: customers, admins (finance, validation, activation, IT)
- **OTP-based authentication**: SMS + email verification for customers, email + password + OTP for admins
- **Registration types**: personal vs company accounts with different approval flows

#### Wallet System
- **Universal wallet**: Every user has one wallet (models/Wallet.php)
- **Payment methods**: 
  - Personal: Credit card via HyperPay only
  - Company: Credit card, bank transfer, bank LC, bank guarantee (requires approval workflow)
- **Balance management**: Deposits, withdrawals, transaction history via WalletTransaction model

#### Service Booking Flow
- **Multi-service orders**: Users can book multiple RFID services in single transaction
- **Vehicle management**: Save/reuse vehicle information across bookings
- **Service types**: RFID chips for cars/trucks with fuel pre-loading
- **Status progression**: pending � approved � completed (RFID assignment by delivery agents)

#### Approval Workflow Engine
- **Dynamic BPM**: Finance � Validation � Activation flow (configurable via admin UI)
- **Multi-step approvals**: Each step requires specific role permissions
- **Notification cascade**: Email & SMS notifications to current approver in chain
- **Audit trail**: Complete history with comments and timestamps

### Payment Integration Architecture

#### HyperPay Integration
- **Dual entity setup**: Separate entity IDs for VISA/MasterCard vs MADA cards
- **Widget embedding**: Dynamic form generation with JavaScript widget loading
- **Status verification**: Secure callback handling with order validation
- **Error handling**: Comprehensive error mapping for user-friendly messages

#### Payment Flow Patterns
1. **Wallet Top-up**: `WalletController@getHyperpayForm` � HyperPay widget � `WalletController@hyperpayStatus`
2. **Service Booking**: `ServiceBookingController@getHyperpayForm` � HyperPay widget � `ServiceBookingController@hyperpayStatus`
3. **Success handling**: Redirect to appropriate history page with status updates

### Key Model Relationships

```
User
   Wallet (1:1) � WalletTransactions (1:many)
   Vehicles (1:many) � ServiceBookings (1:many)
   Orders (1:many) � ServiceBookings (1:many)
   WalletApprovalRequests (1:many) � WalletApprovalSteps (1:many)

Service � ServiceBooking � Vehicle
Order � ServiceBookings (1:many)
```

### Controller Architecture

#### Service Layer Pattern
- **Controllers**: Thin, handle HTTP concerns only
- **Services**: Business logic (ApprovalService, WalletService, NotificationService)
- **Helpers**: Utility functions (LogHelper, StatusHelper, CustomerNumberHelper)

#### Key Controllers
- **ServiceBookingController**: Multi-service order processing, HyperPay integration
- **WalletController**: Balance management, HyperPay top-ups, approval workflows
- **Admin Controllers**: Separated by role (finance, validation, activation)
- **API Controllers**: JSON endpoints for potential mobile app integration

### Frontend Architecture

#### Blade Structure
- **layouts/app.blade.php**: Main customer layout with Bootstrap 5.3
- **Component-based**: Reusable Blade components for forms, cards, status displays
- **Progressive enhancement**: Server-rendered with JavaScript enhancement

#### JavaScript Patterns
- **Axios for AJAX**: Centralized with CSRF token handling
- **SweetAlert2**: All user notifications and confirmations
- **Modular JS**: ES6+ modules, no global variables
- **HyperPay integration**: Dynamic widget loading with comprehensive error handling

## Important Implementation Notes

### Database Conventions
- **UUID primary keys**: All tables use UUID instead of auto-increment
- **Soft deletes**: Important entities use Laravel soft deletes
- **Timestamped operations**: All transactions have created_at/updated_at with timezone support

### Security Patterns
- **CSRF protection**: All forms include CSRF tokens
- **Role-based access**: Spatie permissions with middleware enforcement
- **Input validation**: Form Request classes for all user inputs
- **SQL injection prevention**: Eloquent ORM usage, no raw queries

### Status Management
- **Consistent status values**: "pending", "approved", "rejected" across all entities
- **Separate payment/delivery status**: payment_status vs delivery_status for service bookings
- **RFID status tracking**: pending � assigned � active lifecycle

### Configuration Files
- **services.php**: HyperPay configuration with test/production entity IDs
- **Routing**: Organized by feature area (web.php has wallet, services, admin sections)
- **Environment**: Database, mail, SMS API configurations

### Common Gotchas
- **HyperPay entity IDs**: Different entities for VISA/Master vs MADA cards
- **Session management**: HyperPay checkout sessions stored for verification
- **Approval flow**: Current step tracking critical for workflow progression
- **Vehicle associations**: Optional linking between service bookings and saved vehicles

## File Organization Patterns

```
app/
   Http/Controllers/
      API/           # JSON endpoints
      Admin/         # Role-specific admin controllers
      Customer/      # Customer-facing features
   Models/            # Eloquent models with UUID traits
   Services/          # Business logic layer
   Helpers/           # Utility functions
   Notifications/     # Email & SMS notifications

resources/views/
   layouts/           # Master templates
   wallet/            # Wallet management views
   services/booking/  # Service booking interface
   admin/             # Admin dashboard views

public/js/             # Custom JavaScript modules
routes/                # Feature-organized routing
```

This architecture supports the complex approval workflows, multi-payment gateway integration, and role-based access patterns that define the JoilYaseeir platform.