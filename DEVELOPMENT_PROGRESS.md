# Development Progress — Aster

**Last updated:** 2026-11-12

## Completed modules

### Phase 0 — Documentation
- SRS, Architecture, Database, Roles, Roadmap, Installation, Environment

### Phase 1 — Foundation
- Auth, Spatie RBAC, dual layouts, business settings, i18n stubs

### Phase 2 — Foundation polish
- `MediaUploader`, notifications table (from Phase 1), admin CRUD patterns across modules

### Phase 3 — Catalog & Inventory
- Categories, products, variants, suppliers, stock adjustments, inventory transactions
- Admin UI + demo sample products (clearly labeled sample data)

### Phase 4 — E-commerce
- Shop, product detail, cart (guest token), checkout, order creation with stock deduction
- Customer account order history
- Admin order management + status transitions

### Phase 5 — Payments & Custom orders
- Payment methods, partial/full payments, verification
- Custom order requests, measurements/images, quotations
- Feature flags respected (`custom_orders_enabled`, `guest_checkout_enabled`)

### Phase 6 — Production & Delivery
- Configurable production stages + jobs/stage updates
- Delivery zones (sample), delivery records created with orders, assign/status updates

### Phase 7 — CRM & Marketing
- Customer admin index/show
- Coupons, promotions, social posts, newsletter subscribe
- Wishlist models present; reviews/expenses tables ready

### Phase 8 — Dashboard & Reporting
- Live dashboard stats (sales, orders, customers, low stock, custom pipeline)
- Sales / inventory / payments report pages + Excel sales export (`maatwebsite/excel`)
- Chart.js / vue-chartjs installed for future chart widgets

## In progress

- None for Phases 2–8 core delivery

## Pending modules

- Phase 9 — Expanded QA / security / N+1 hardening
- Phase 10 — Deployment prep

## Known bugs / notes

- Excel export needs PHP `ext-zip` enabled in Laragon for downloads
- Delivery zone fees and product prices in seeders are **sample placeholders**
- Tailwind remains v3 (Breeze-compatible)
- Wishlist/reviews UI not fully exposed on storefront yet (schema + models ready)

## Technical decisions

- Guest carts use stable `cart_token` in session (not raw session id)
- Money as `decimal(12,2)`; inventory changes always write transactions
- Order line snapshots preserve name/SKU/price
- Payments verified separately from recording

## Demo credentials (local only)

| Account | Email | Password |
|---------|-------|----------|
| Owner | `owner@aster.test` | `password` |
| Customer | `customer@aster.test` | `password` |

## Next implementation steps

1. Phase 9: broaden automated tests, authorization matrix, query optimization
2. Phase 10: production env, queues/scheduler docs, backups
3. Optional polish: storefront wishlists/reviews UI, dashboard Chart.js widgets, Telebirr/Chapa interfaces
