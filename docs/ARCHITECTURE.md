# System Architecture — Aster

## Overview

Modular monolithic Laravel 12 application with Vue 3 + Inertia 2 frontends.

- **Backend:** Laravel 12, PHP 8.2+, MySQL/MariaDB, Eloquent, Form Requests, Policies, Notifications, Queues, Storage  
- **Frontend:** Vue 3 Composition API, Inertia 2, Tailwind CSS 4, Lucide icons  
- **RBAC:** Spatie Laravel Permission  

## Domain layout

```
app/
  Domains/
    Auth/
    Settings/
    Catalog/
    Inventory/
    Customers/
    Orders/
    Payments/
    CustomOrders/
    Production/
    Delivery/
    Marketing/
    Reports/
    Dashboard/
  Http/Controllers/
    Admin/
    Storefront/
    Auth/
  Http/Middleware/
  Http/Requests/
resources/js/
  Pages/
    Admin/
    Storefront/
    Auth/
    Profile/
  Layouts/
  Components/
    UI/
    Admin/
    Storefront/
routes/
  web.php
  admin.php
  storefront.php
  auth.php
```

Each domain may contain: `Models/`, `Actions/`, `Enums/`, `Policies/`, `Services/`.

Controllers stay thin; multi-step business operations live in Action/Service classes inside transactions.

## Request flow

1. Browser → Laravel web routes  
2. Middleware: session, CSRF, Inertia, optional permission middleware  
3. Form Request validation  
4. Policy / Gate authorization  
5. Action/Service → Eloquent  
6. Inertia response or redirect with flash  

## Auth model

- Single `users` table  
- Staff roles: owner, manager, salesperson, inventory_manager, production_staff, delivery_staff  
- Storefront portal users: `customer` role + linked `customers` row  
- Guest checkout supported when `guest_checkout_enabled` is true  

## Admin vs storefront

| Surface | Path prefix | Layout |
|---------|-------------|--------|
| Storefront | `/` | `StorefrontLayout` — premium fashion |
| Customer account | `/account` | Storefront + account nav |
| Admin | `/admin` | `AdminLayout` — dense ops UI |
| Staff login | `/admin/login` | Auth layout |
| Customer auth | `/login`, `/register` | Auth / storefront |

## Cross-cutting

- **Settings:** `SettingsService` reads `business_settings`; shared to Inertia as `brand`  
- **Notifications:** database + mail initially; SMS/Telegram/WhatsApp later  
- **Files:** Laravel Storage; validated image types/sizes  
- **Queues:** database driver locally; Redis recommended in production  
- **Payments:** manual recording first; Telebirr/Chapa via future gateway interfaces  

## Security

- Mass assignment protection via `$fillable` / `$guarded`  
- Customers may only access own orders/addresses  
- Staff limited by Spatie permissions  
- No raw card storage  
