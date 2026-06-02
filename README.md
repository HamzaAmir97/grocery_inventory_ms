<div align="center">

# Grocery Inventory Management System

A professional full-stack grocery inventory dashboard for managing stock, categories, subcategories, units, suppliers, low-stock alerts, and operational reporting.

[![Frontend](https://img.shields.io/badge/Frontend-Next.js-blue)](./frontend/README.md)
[![Backend](https://img.shields.io/badge/Backend-Laravel-red)](./backend/README.md)
[![Database](https://img.shields.io/badge/Database-PostgreSQL-336791?logo=postgresql)](https://www.postgresql.org/)
[![Auth](https://img.shields.io/badge/Auth-JWT-111827)](./backend/README.md)
[![API Docs](https://img.shields.io/badge/API-Swagger-85EA2D?logo=swagger)](./backend/grocery-inventory-backend/README.md)
[![CI Quality Gate](https://github.com/HamzaAmir97/inventory_managment_system_test/actions/workflows/ci.yml/badge.svg)](https://github.com/HamzaAmir97/inventory_managment_system_test/actions/workflows/ci.yml)

</div>

> The main assessment rule is simple: categories, subcategories, units, and suppliers are never hardcoded in the frontend. They come from database-backed API endpoints.

---

## Table of Contents

- [Project Purpose](#project-purpose)
- [Features](#features)
- [Technologies Used](#technologies-used)
  - [Frontend](#frontend)
  - [Backend](#backend)
- [Project Structure](#project-structure)
- [Architecture Summary](#architecture-summary)
- [Frontend Overview](#frontend-overview)
- [Backend Overview](#backend-overview)
- [API Surface](#api-surface)
- [CI/CD Quality Gate](#cicd-quality-gate)
- [Local URLs](#local-urls)
- [Design Reference](#design-reference)
- [Getting Started](#getting-started)
- [Usage Flow](#usage-flow)
- [Important Notes](#important-notes)
- [License](#license)

---

## Project Purpose

This system is built as a clean, review-ready inventory management platform. It demonstrates:

- A modern dashboard-only admin experience
- Strong frontend/backend separation
- Database-driven settings and lookup values
- JWT-protected API integration
- PostgreSQL-backed data integrity
- Swagger/OpenAPI documentation
- Practical tests around authentication, settings, inventory, and dashboard behavior
- A GitHub Actions quality gate that validates backend and frontend changes before merge

---

## Features

- **Admin Authentication:** JWT login, protected admin routes, logout, refresh, and global 401 handling.
- **Dashboard Overview:** Total items, categories, suppliers, low-stock count, total stock value, recent items, and low-stock list.
- **Inventory Management:** Item listing, filters, pagination, create, edit, delete, optimistic locking, soft deletes, and low-stock highlighting.
- **Settings Management:** CRUD pages for categories, subcategories, units, and suppliers.
- **Database-Driven Dropdowns:** Lookup endpoints power all business dropdown values.
- **Safe Deletes:** Backend prevents deleting records already used by inventory items or related records.
- **Stock Movement History:** Inventory stock changes are stored as audit records for review and troubleshooting.
- **Security Layer:** Rate limits, CORS rules, request IDs, security headers, authorization policies, and safe error responses.
- **Swagger Documentation:** Interactive API docs for all backend endpoints.
- **Professional Architecture:** Frontend uses API paths, feature actions, TanStack Query hooks, schemas, helpers, and typed contracts.
- **CI/CD Readiness:** GitHub Actions runs backend and frontend checks before code is accepted into the deployment branch.

---

## Technologies Used

### Frontend

[![Next.js](https://img.shields.io/badge/Next.js-16-black?logo=next.js)](https://nextjs.org/)
[![React](https://img.shields.io/badge/React-19-61DAFB?logo=react)](https://react.dev/)
[![TypeScript](https://img.shields.io/badge/TypeScript-Strict-3178C6?logo=typescript)](https://www.typescriptlang.org/)
[![Axios](https://img.shields.io/badge/Axios-API_Client-5A29E4)](https://axios-http.com/)
[![TanStack Query](https://img.shields.io/badge/TanStack_Query-Server_State-FF4154)](https://tanstack.com/query)

### Backend

[![Laravel](https://img.shields.io/badge/Laravel-13-red?logo=laravel)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?logo=php)](https://www.php.net/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-Database-336791?logo=postgresql)](https://www.postgresql.org/)
[![JWT](https://img.shields.io/badge/JWT-Authentication-111827)](https://jwt.io/)
[![Swagger](https://img.shields.io/badge/Swagger-OpenAPI-85EA2D?logo=swagger)](https://swagger.io/)
[![Pest](https://img.shields.io/badge/Pest-Tests-6E9F18)](https://pestphp.com/)

---

## Project Structure

```txt
project/
|-- .github/workflows/ci.yml              # GitHub Actions quality gate
|-- frontend/                             # Next.js dashboard
|   |-- public/
|   |-- src/
|   |   |-- app/                          # Route groups and pages
|   |   |-- components/                   # UI and feature components
|   |   |-- constants/                    # Static UI configuration
|   |   |-- hooks/                        # React Query hooks
|   |   |-- lib/                          # Axios, API paths, actions, schemas, helpers
|   |   `-- types/                        # TypeScript contracts
|   |-- package.json
|   `-- README.md
|
|-- backend/
|   |-- README.md                         # Backend workspace entry
|   `-- grocery-inventory-backend/        # Laravel REST API
|       |-- app/
|       |-- database/
|       |-- routes/
|       |-- tests/
|       |-- composer.json
|       `-- README.md
|
|-- github-actions-ci-cd-explanation.html # CI/CD workflow explanation
`-- README.md                             # Project overview
```

---

## Architecture Summary

```txt
Frontend Components
  -> React Query Hooks
  -> Feature Actions
  -> Axios Instance
  -> Central API Paths
  -> Laravel REST API
  -> PostgreSQL

GitHub Actions
  -> Backend checks + PostgreSQL migrations/tests
  -> Frontend lint/typecheck/tests/build
  -> Quality gate before merge
  -> Coolify deployment from protected branch
```

The frontend owns UI state and server-state coordination. The backend owns validation, authentication, persistence, lookup data, safe deletes, authorization, audit history, and API documentation.

---

## Frontend Overview

The frontend is a Next.js dashboard with route groups for authentication and admin pages. Its API layer is centralized and typed:

- `src/lib/api-paths.ts` contains all endpoint paths.
- `src/lib/axios-instance.ts` handles base URL, token injection, and 401 redirects.
- `src/lib/{feature}/actions` contains raw API functions, query keys, query options, and mutation options.
- `src/hooks/{feature}` exposes TanStack Query hooks for components.

<p>
  <a href="./frontend/README.md"><strong>Read more about the frontend</strong></a>
</p>

---

## Backend Overview

The backend is a Laravel REST API with JWT authentication, PostgreSQL, Swagger/OpenAPI documentation, resource responses, service classes, authorization policies, and feature tests.

Core API areas:

- Authentication and token refresh
- Dashboard stats
- Inventory items
- Stock movement history
- Settings CRUD
- Lookup endpoints
- Status endpoint
- Canonical `/api/*` routes with `/api/v1/*` aliases

<p>
  <a href="./backend/README.md"><strong>Read more about the backend</strong></a>
</p>

---

## API Surface

The current frontend uses the canonical API base URL:

```txt
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
```

The backend also exposes the same protected resources under `/api/v1/*` for future versioned clients. Both forms are intentionally supported:

| Area            | Canonical                                                               | Versioned alias                  |
| --------------- | ----------------------------------------------------------------------- | -------------------------------- |
| Auth            | `/api/auth/*`                                                           | `/api/v1/auth/*`                 |
| Dashboard       | `/api/dashboard/stats`                                                  | `/api/v1/dashboard/stats`        |
| Inventory       | `/api/items`                                                            | `/api/v1/items`                  |
| Stock movements | `/api/items/{item}/movements`                                           | `/api/v1/items/{item}/movements` |
| Settings        | `/api/categories`, `/api/subcategories`, `/api/units`, `/api/suppliers` | `/api/v1/...`                    |
| Lookups         | `/api/lookups/*`                                                        | `/api/v1/lookups/*`              |

---

## CI/CD Quality Gate

The repository includes a GitHub Actions workflow at `.github/workflows/ci.yml`. It is used as a CI quality gate before changes are merged into the deployment branch.

What it checks:

- Backend dependency validation and installation
- Laravel Vite asset build
- PostgreSQL service startup
- `migrate:fresh --seed` against a real PostgreSQL database
- OpenAPI generation
- Backend Pest test suite
- Laravel production cache commands
- Frontend dependency installation
- Frontend lint, typecheck, Vitest, and production build

Deployment is handled by Coolify through its webhook. The workflow is intentionally not a second deployment system. For full protection, the `main` branch should require the `Quality gate` status check before merge. With that setup, broken code cannot reach the branch that Coolify deploys from.
   



   

## Local URLs

| Service     | URL                                     |
| ----------- | --------------------------------------- |
| Frontend    | http://localhost:3000                   |
| Backend API | http://127.0.0.1:8000/api               |
| Swagger UI  | http://127.0.0.1:8000/api/documentation |

## Live Demo

| Service            | URL                                                    |
| ------------------ | ------------------------------------------------------ |
| Frontend dashboard | https://test.hamzahamir.site                           |
| Backend API        | https://backend-test.hamzahamir.site/api               |
| Swagger / OpenAPI  | https://backend-test.hamzahamir.site/api/documentation |
| Backend status     | https://backend-test.hamzahamir.site/api/status        |

## Design Reference

| Asset        | URL                                                                                  |
| ------------ | ------------------------------------------------------------------------------------ |
| Figma design | https://www.figma.com/design/dnTSLh378IvJRopqkbx752/test?m=auto&t=wF3BqhssfDjsLKFZ-1 |

---

## Getting Started

### Backend

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

### Frontend

```bash
cd frontend
npm install
npm run dev
```

Open the dashboard:

```txt
http://localhost:3000
```

Demo admin:

```txt
Email:    admin@example.com
Password: password
```

---

## Usage Flow

1. Start PostgreSQL and the Laravel API.
2. Seed the database with demo settings and inventory data.
3. Start the Next.js frontend.
4. Log in with the seeded admin account.
5. Review dashboard metrics.
6. Manage inventory items.
7. Manage categories, subcategories, units, and suppliers from Settings.
8. Use Swagger UI to inspect or test backend endpoints.
9. Open a pull request and let the `Quality gate` check pass before merging to the deployment branch.

---

## Important Notes

- Business dropdown values must come from lookup APIs.
- Protected backend endpoints require `Authorization: Bearer <token>`.
- The dashboard endpoint is `GET /api/dashboard/stats`.
- Versioned aliases are available under `/api/v1/*`.
- The frontend API base URL is configured with `NEXT_PUBLIC_API_BASE_URL`.
- The backend CORS origin must include `http://localhost:3000`.
- Coolify handles deployment; GitHub Actions handles validation before merge.

---

## License

Copyright 2026 Grocery Inventory Management System. Use for learning, review, and portfolio purposes unless another license is added.
