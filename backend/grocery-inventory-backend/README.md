<p align="right">
  <a href="../../README.md"><strong>Back to Home</strong></a>
</p>

---

<div align="center">

# Grocery Inventory Backend API

The Laravel REST API powering authentication, inventory, settings, lookups, dashboard metrics, stock movement history, and Swagger documentation.

[![Laravel](https://img.shields.io/badge/Laravel-13-red?logo=laravel)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php)](https://www.php.net/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Database-336791?logo=postgresql)](https://www.postgresql.org/)
[![JWT](https://img.shields.io/badge/JWT-Auth-111827)](https://jwt.io/)
[![Swagger](https://img.shields.io/badge/Swagger-OpenAPI-85EA2D?logo=swagger)](https://swagger.io/)
[![Pest](https://img.shields.io/badge/Pest-Tests-6E9F18)](https://pestphp.com/)
[![CI Quality Gate](https://github.com/HamzaAmir97/inventory_managment_system_test/actions/workflows/ci.yml/badge.svg)](https://github.com/HamzaAmir97/inventory_managment_system_test/actions/workflows/ci.yml)

</div>

> The backend owns the source of truth for inventory data, settings data, lookup values, validation rules, authorization, delete restrictions, audit history, and API documentation.

---

## Table of Contents

- [Project Purpose](#project-purpose)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [Key Files](#key-files)
- [Environment Variables](#environment-variables)
- [Getting Started](#getting-started)
- [Production Deployment](#production-deployment)
- [API Versioning](#api-versioning)
- [API Endpoints](#api-endpoints)
- [Swagger Documentation](#swagger-documentation)
- [Testing](#testing)
- [CI/CD Quality Gate](#cicd-quality-gate)
- [Important Notes](#important-notes)

---

## Project Purpose

This backend provides a secure, documented REST API for a grocery inventory dashboard. It manages authentication, inventory items, settings, lookups, dashboard metrics, authorization, audit history, and integrity constraints.

It enables the frontend to:

- Authenticate an admin user
- Refresh and invalidate JWT tokens
- Fetch dashboard stats
- Manage inventory items
- Track stock movement history for items
- Manage database-backed settings
- Load lookup values for dropdowns
- Handle validation, delete conflicts, and optimistic-lock conflicts safely

---

## Features

- **JWT Authentication:** Login, current user, logout, refresh, protected API routes, and throttled login attempts.
- **Authorization Policies:** Resource-level policies for inventory and settings actions.
- **Dashboard Stats:** Counts, stock value, recent items, and low-stock list.
- **Inventory API:** Full CRUD with filters, pagination, validation, optimistic locking, soft deletes, and stock movement logging.
- **Settings API:** CRUD for categories, subcategories, units, and suppliers.
- **Lookup API:** Active database-backed options for frontend dropdowns.
- **Delete Guards:** Prevent unsafe deletion when records are in use.
- **Data Integrity:** PostgreSQL foreign keys, check constraints, partial unique indexes, and cross-relation validation.
- **Security Middleware:** Request IDs, security headers, CORS policy, rate limiting, safe logging, and sanitized production errors.
- **Swagger/OpenAPI:** Interactive and review-ready API documentation.
- **Tests:** Feature tests for auth, authorization, dashboard, settings, inventory, integrity, CORS, Swagger, error envelopes, and server experience.

---

## Technologies Used

[![Laravel](https://img.shields.io/badge/Laravel-REST_API-red?logo=laravel)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php)](https://www.php.net/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Relational_Data-336791?logo=postgresql)](https://www.postgresql.org/)
[![JWT Auth](https://img.shields.io/badge/tymon%2Fjwt--auth-JWT-111827)](https://jwt-auth.readthedocs.io/)
[![L5 Swagger](https://img.shields.io/badge/L5--Swagger-OpenAPI-85EA2D?logo=swagger)](https://github.com/DarkaOnLine/L5-Swagger)
[![Pest](https://img.shields.io/badge/Pest-Test_Suite-6E9F18)](https://pestphp.com/)

---

## Project Structure

```txt
grocery-inventory-backend/
|-- app/
|   |-- Actions/Inventory/           # Transactional write operations
|   |-- Exceptions/                  # Domain exceptions
|   |-- Http/
|   |   |-- Controllers/Api/          # REST controllers
|   |   |-- Middleware/               # API middleware
|   |   |-- OpenApi/                  # Swagger schemas and security
|   |   |-- Requests/                 # Form requests and shared validation rules
|   |   `-- Resources/                # API resources
|   |-- Models/                      # Eloquent models
|   |-- Policies/                    # Resource authorization
|   |-- Services/                    # Business and query services
|   `-- Support/                     # API response and logging helpers
|-- database/
|   |-- migrations/
|   |-- seeders/
|   `-- factories/
|-- routes/
|   `-- api.php
|-- tests/
|   |-- Feature/
|   `-- Unit/
|-- .env.example
|-- composer.json
|-- package.json
|-- package-lock.json
`-- README.md
```

---

## Key Files

- `routes/api.php`: Canonical API route definitions and `/api/v1` aliases
- `app/Http/Controllers/Api`: Auth, dashboard, inventory, settings, lookup, and status controllers
- `app/Actions/Inventory`: Store, update, and delete operations wrapped in database transactions
- `app/Policies`: Resource authorization policies
- `app/Services/DashboardService.php`: Dashboard aggregation logic
- `app/Services/DeleteGuardService.php`: Safe delete checks
- `app/Http/OpenApi`: Swagger/OpenAPI definitions and reusable schemas
- `app/Http/Middleware`: Request ID, security headers, and request logging
- `app/Support/ApiResponse.php`: Consistent API response envelope
- `app/Support/LogScrubber.php`: Sensitive-field scrubbing for logs
- `database/migrations`: PostgreSQL schema, constraints, indexes, soft deletes, and stock movements
- `database/seeders/AdminUserSeeder.php`: Demo admin account
- `tests/Feature`: Backend regression and contract tests

---

## Environment Variables

Create `.env` from `.env.example`:

```bash
cp .env.example .env
```

Important variables:

```env
APP_URL=http://localhost:8000
APP_KEY=

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=grocery_inventory_backend
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

JWT_SECRET=
JWT_TTL=60
JWT_REFRESH_TTL=20160

DASHBOARD_ALLOWED_ORIGINS=http://localhost:3000
L5_SWAGGER_CONST_HOST=http://localhost:8000
```

---

## Getting Started

### Prerequisites

- PHP 8.3+
- Composer
- PostgreSQL
- Node.js 20+ for Laravel Vite assets

### Setup

```bash
cd backend/grocery-inventory-backend

composer install
npm ci
npm run build
cp .env.example .env

php artisan key:generate
php artisan jwt:secret
php artisan migrate:fresh --seed
php artisan serve
```

Backend URL:

```txt
http://127.0.0.1:8000
```

Seeded admin:

```txt
Email:    admin@example.com
Password: password
```

## Live API

| Service            | URL                                                    |
| ------------------ | ------------------------------------------------------ |
| Backend API        | https://backend-test.hamzahamir.site/api               |
| Versioned API base | https://backend-test.hamzahamir.site/api/v1            |
| Swagger / OpenAPI  | https://backend-test.hamzahamir.site/api/documentation |
| OpenAPI JSON       | https://backend-test.hamzahamir.site/docs              |
| Backend status     | https://backend-test.hamzahamir.site/api/status        |

---

## Production Deployment

The current production backend domain is:

```txt
https://backend-test.hamzahamir.site
```

For Cloudflare, use a proxied `A` record:

```txt
Name: backend-test
Target: <server-ip>
Proxy status: Proxied
```

For Coolify with Nixpacks:

```txt
Ports Exposes: 8080
Port Mappings: empty
Start Command: php artisan optimize:clear && php artisan l5-swagger:generate && php artisan serve --host=0.0.0.0 --port=8080
Build Command: php artisan optimize:clear
Pre-deployment Command: php artisan migrate --force
```

Required production environment variables:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:generated_application_key
APP_URL=https://backend-test.hamzahamir.site
LOG_CHANNEL=stderr

DB_CONNECTION=pgsql
DB_HOST=internal_postgres_hostname
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=postgres_password

JWT_SECRET=generated_jwt_secret
JWT_TTL=60
JWT_REFRESH_TTL=20160

L5_SWAGGER_CONST_HOST=https://backend-test.hamzahamir.site
L5_SWAGGER_USE_ABSOLUTE_PATH=false
DASHBOARD_ALLOWED_ORIGINS=https://test.hamzahamir.site,https://www.test.hamzahamir.site
```

Notes:

- `DB_HOST` must be the internal PostgreSQL hostname only, not the full `postgres://...` URL.
- Use `DB_DATABASE=postgres` unless a separate database named `inventorydb` has been created.
- `APP_KEY` must be generated with `php artisan key:generate --show` and copied exactly, including the `base64:` prefix.
- `JWT_SECRET` must be generated with `php artisan jwt:secret --show` or equivalent secure secret generation.
- `L5_SWAGGER_USE_ABSOLUTE_PATH=false` prevents Swagger UI mixed-content errors behind HTTPS proxies.
- `php artisan l5-swagger:generate` must run inside the runtime container before the server starts so `/app/storage/api-docs/api-docs.json` exists.
- Coolify handles deployment. GitHub Actions should be used as a required quality gate before code reaches the branch deployed by Coolify.

---

## API Versioning

The API exposes two route surfaces:

- Canonical routes under `/api/*`
- Versioned aliases under `/api/v1/*`

The current frontend uses the canonical `/api` base URL. The `/api/v1` aliases are available for future versioned clients without breaking existing integrations.

---

## API Endpoints

Protected endpoints require:

```txt
Authorization: Bearer <token>
Accept: application/json
```

| Method      | Endpoint                     | Auth     | Purpose                     |
| ----------- | ---------------------------- | -------- | --------------------------- |
| `POST`      | `/api/auth/login`            | Public   | Admin login                 |
| `GET`       | `/api/status`                | Public   | Public API status           |
| `GET`       | `/docs`                      | Public   | OpenAPI JSON document       |
| `GET`       | `/api/auth/me`               | Required | Current authenticated user  |
| `POST`      | `/api/auth/logout`           | Required | Logout and invalidate token |
| `POST`      | `/api/auth/refresh`          | Required | Refresh JWT token           |
| `GET`       | `/api/dashboard/stats`       | Required | Dashboard metrics           |
| `GET`       | `/api/items`                 | Required | Inventory list              |
| `POST`      | `/api/items`                 | Required | Create inventory item       |
| `GET`       | `/api/items/{id}`            | Required | Inventory detail            |
| `PUT/PATCH` | `/api/items/{id}`            | Required | Update inventory item       |
| `DELETE`    | `/api/items/{id}`            | Required | Delete inventory item       |
| `GET`       | `/api/items/{id}/movements`  | Required | Item stock movement history |
| `GET`       | `/api/categories`            | Required | Category list               |
| `POST`      | `/api/categories`            | Required | Create category             |
| `GET`       | `/api/categories/{id}`       | Required | Category detail             |
| `PUT/PATCH` | `/api/categories/{id}`       | Required | Update category             |
| `DELETE`    | `/api/categories/{id}`       | Required | Delete category             |
| `GET`       | `/api/subcategories`         | Required | Subcategory list            |
| `POST`      | `/api/subcategories`         | Required | Create subcategory          |
| `GET`       | `/api/subcategories/{id}`    | Required | Subcategory detail          |
| `PUT/PATCH` | `/api/subcategories/{id}`    | Required | Update subcategory          |
| `DELETE`    | `/api/subcategories/{id}`    | Required | Delete subcategory          |
| `GET`       | `/api/units`                 | Required | Unit list                   |
| `POST`      | `/api/units`                 | Required | Create unit                 |
| `GET`       | `/api/units/{id}`            | Required | Unit detail                 |
| `PUT/PATCH` | `/api/units/{id}`            | Required | Update unit                 |
| `DELETE`    | `/api/units/{id}`            | Required | Delete unit                 |
| `GET`       | `/api/suppliers`             | Required | Supplier list               |
| `POST`      | `/api/suppliers`             | Required | Create supplier             |
| `GET`       | `/api/suppliers/{id}`        | Required | Supplier detail             |
| `PUT/PATCH` | `/api/suppliers/{id}`        | Required | Update supplier             |
| `DELETE`    | `/api/suppliers/{id}`        | Required | Delete supplier             |
| `GET`       | `/api/lookups/categories`    | Required | Category dropdown data      |
| `GET`       | `/api/lookups/subcategories` | Required | Subcategory dropdown data   |
| `GET`       | `/api/lookups/units`         | Required | Unit dropdown data          |
| `GET`       | `/api/lookups/suppliers`     | Required | Supplier dropdown data      |

The API route group is mirrored under `/api/v1/...`; `/docs` and Swagger UI remain documentation endpoints outside the versioned API group.

---

## Swagger Documentation

Regenerate documentation:

```bash
php artisan l5-swagger:generate
```

Open Swagger UI:

```txt
http://127.0.0.1:8000/api/documentation
```

OpenAPI JSON:

```txt
http://127.0.0.1:8000/docs
```

For protected endpoints, authorize with:

```txt
Bearer <token>
```

---

## Testing

```bash
php artisan test
```

or:

```bash
composer test
```

Current suite coverage includes authentication, authorization, settings, inventory, dashboard, integrity constraints, server experience, Swagger/OpenAPI contracts, CORS, and error envelope behavior.

---

## CI/CD Quality Gate

The repository workflow lives at:

```txt
.github/workflows/ci.yml
```

Backend steps in the workflow:

- Start a PostgreSQL 16 service
- Set up PHP 8.3 with PostgreSQL extensions
- Validate `composer.json`
- Install Composer dependencies
- Install Node dependencies with `npm ci`
- Build Laravel Vite assets
- Prepare `.env` from `.env.example`
- Run `php artisan migrate:fresh --seed`
- Check migration status
- Generate OpenAPI documentation
- Run the Pest test suite
- Verify production cache commands: `config:cache`, `route:cache`, `view:cache`, `optimize:clear`

This workflow does not deploy the application. Coolify is responsible for deployment through its webhook. The intended release flow is:

```txt
feature branch
  -> pull request
  -> GitHub Actions Quality gate passes
  -> merge into main
  -> Coolify deploys main
```

Detailed explanation:

```txt
../../github-actions-ci-cd-explanation.html
```

---

## Important Notes

- Lookup endpoints are the only source for frontend dropdown business data.
- Delete guards prevent removing records currently used by inventory items or related records.
- Dashboard data is served from `GET /api/dashboard/stats`.
- Stock movement history is served from `GET /api/items/{id}/movements`.
- The frontend origin must be allowed in CORS through `DASHBOARD_ALLOWED_ORIGINS`.
- Swagger should be regenerated whenever endpoint contracts change.
- Enable branch protection on `main` and require the `Quality gate` check before merge for CI to protect the Coolify deployment branch.
