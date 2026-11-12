# Module Implementation Roadmap — Aster

## Phase 0 — Analysis & documentation

Inspect stack, produce SRS/architecture/DB/roles docs. **(this milestone)**

## Phase 1 — Foundation

Auth, Spatie RBAC, dual layouts, business settings, domain folders, i18n stubs, seeders, smoke tests.

## Phase 2 — Foundation polish

Admin CRUD patterns, media upload helper, notification channel base, shared table/filter components.

## Phase 3 — Catalog & inventory

Categories, products, variants, attributes, images, inventory transactions, suppliers, purchases, low-stock.

## Phase 4 — E-commerce

Public site pages, cart (guest + auth), checkout, customer account, order creation & admin order management.

## Phase 5 — Payments & custom orders

Partial payments, verification, custom requests, measurements, quotations, approvals, deposits.

## Phase 6 — Production & delivery

Configurable stages, jobs, assignments, delivery zones/fees, status & POD.

## Phase 7 — CRM & marketing

Segmentation foundation, wishlists, reviews, coupons, newsletter, social showcase (manual).

## Phase 8 — Dashboard & reporting

Real analytics queries, charts, Excel/CSV exports, operational summaries.

## Phase 9 — QA

Expanded automated tests, authorization matrix tests, security review, performance/N+1 fixes.

## Phase 10 — Deployment prep

Production env docs, storage/queues/scheduler, backups, deployment guide.

## Dependency notes

- Orders depend on Catalog + Inventory  
- Payments depend on Orders  
- Production depends on Custom Orders / Orders  
- Delivery depends on Orders  
- Dashboard/Reports depend on data from prior phases  
- Gateways (Telebirr/Chapa) after manual payments are stable  
