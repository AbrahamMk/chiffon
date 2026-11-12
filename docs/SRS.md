# Software Requirements Specification — Aster Fashion Platform

**Version:** 0.1  
**Status:** Living document  
**Working brand name:** Aster (configurable)

## 1. Purpose

Aster is a fashion e-commerce and business management platform for an Ethiopian fashion brand associated with Aster Weregnawa. It combines a public brand website, e-commerce, inventory, orders, payments, custom clothing, production, CRM, delivery, marketing, and analytics.

## 2. Non-goals / constraints

Do **not** invent as real business data:

- Official registration details, physical addresses, phone numbers
- Product prices or availability
- Testimonials, production capacity, delivery arrangements
- Payment gateway credentials or policies

These must be configurable via administration settings. Placeholder/demo data must be clearly labeled as sample.

## 3. Users

| Actor | Description |
|-------|-------------|
| Guest | Browse catalog, guest cart/checkout (if enabled) |
| Customer | Account, orders, wishlist, custom requests, reviews |
| Owner | Full system access |
| Manager | Daily operations |
| Salesperson | Customers, orders, authorized payments |
| Inventory Manager | Products, stock, suppliers |
| Production Staff | Assigned production jobs |
| Delivery Staff | Assigned deliveries |

Permissions are enforced individually (Spatie), not by hardcoded role names alone.

## 4. Functional modules

1. **Public website** — home, shop, collections, product detail, cart, checkout, about, contact, custom order form  
2. **Catalog** — categories, products, variants, attributes, images, pricing, featured flags  
3. **Inventory** — stock at variant level, transactions, suppliers, purchases, low-stock alerts  
4. **Orders** — lifecycle, snapshots, discounts, staff notes, invoices  
5. **Payments** — cash/transfer/manual, partial payments, verification (gateways later)  
6. **Custom orders** — requests, measurements, quotations, approval, deposits  
7. **Production** — configurable stages, job assignment, progress  
8. **CRM** — customers, addresses, history, loyalty foundation  
9. **Delivery** — zones (configurable), assignment, POD  
10. **Marketing** — banners, coupons, newsletter, manual social content  
11. **Dashboard & reports** — real queries only; Excel/CSV export  
12. **Settings** — brand, locale, currency (default ETB), feature flags  

## 5. Non-functional requirements

- Security: CSRF, authz policies, validated uploads, rate limits on sensitive endpoints  
- Money: decimal columns only; never trust browser prices  
- Stock: never silent mutations; full transaction history  
- i18n: English + Amharic via Laravel localization  
- Timezone default: `Africa/Addis_Ababa`  
- Responsive, accessible UI; distinct storefront vs admin aesthetics  

## 6. Definition of done (per module)

Migrations, models, validation, authorization, business logic, functional UI, empty/error/loading states, automated tests for critical paths.
