# Role & Permission Matrix — Aster

Permissions are checked individually. Roles are convenience bundles.

## Permissions (seeded)

| Permission | Description |
|------------|-------------|
| `dashboard.view` | View admin dashboard |
| `users.manage` | Manage employees |
| `roles.manage` | Manage roles & permissions |
| `products.manage` | Manage catalog |
| `inventory.manage` | Stock, suppliers, purchases |
| `customers.manage` | CRM |
| `orders.view` | View orders |
| `orders.manage` | Create/update orders |
| `payments.manage` | Record payments |
| `payments.verify` | Verify payments |
| `custom_orders.manage` | Custom order workflows |
| `production.manage` | Manage production jobs |
| `production.update` | Update assigned production |
| `deliveries.manage` | Manage deliveries |
| `deliveries.update` | Update assigned deliveries |
| `expenses.manage` | Expenses |
| `reports.view` | Operational reports |
| `reports.financial` | Financial reports |
| `settings.manage` | Business settings |
| `marketing.manage` | Banners, coupons, social |

## Role bundles

| Role | Permissions |
|------|-------------|
| **owner** | All permissions |
| **manager** | dashboard, products, inventory, customers, orders.*, payments.manage, custom_orders, production.manage, deliveries.manage, reports.view, marketing; financial/settings configurable |
| **salesperson** | dashboard.view, customers.manage, orders.view, orders.manage, payments.manage |
| **inventory_manager** | dashboard.view, products.manage, inventory.manage, reports.view |
| **production_staff** | dashboard.view, production.update, custom_orders.manage (read assigned specs via policy) |
| **delivery_staff** | dashboard.view, deliveries.update |
| **customer** | Storefront account only (no admin permissions) |

Manager financial access (`reports.financial`, `payments.verify`, `expenses.manage`, `settings.manage`) defaults **off** and can be granted later.
