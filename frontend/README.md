<p align="right">
  <a href="../README.md"><strong>Back to Home</strong></a>
</p>

<div align="center">

# Grocery Inventory Frontend

The Next.js dashboard client for the Grocery Inventory Management System.

[![Next.js](https://img.shields.io/badge/Next.js-16-black?logo=next.js)](https://nextjs.org/)
[![React](https://img.shields.io/badge/React-19-61DAFB?logo=react)](https://react.dev/)
[![TypeScript](https://img.shields.io/badge/TypeScript-Strict-3178C6?logo=typescript)](https://www.typescriptlang.org/)
[![TanStack Query](https://img.shields.io/badge/TanStack_Query-Server_State-FF4154)](https://tanstack.com/query)
[![Axios](https://img.shields.io/badge/Axios-API_Client-5A29E4)](https://axios-http.com/)
[![CI Quality Gate](https://github.com/HamzaAmir97/inventory_managment_system_test/actions/workflows/ci.yml/badge.svg)](https://github.com/HamzaAmir97/inventory_managment_system_test/actions/workflows/ci.yml)

</div>

> This frontend is built around a strict API architecture: components use hooks, hooks use feature actions, actions use the shared Axios instance, and endpoint paths live only in `src/lib/api-paths.ts`.

---

## Table of Contents

- [Project Purpose](#project-purpose)
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [Key Files](#key-files)
- [Environment Variables](#environment-variables)
- [Getting Started](#getting-started)
- [Design Reference](#design-reference)
- [Routes](#routes)
- [API Integration](#api-integration)
- [Data Flow](#data-flow)
- [Build and Checks](#build-and-checks)
- [CI/CD Quality Gate](#cicd-quality-gate)
- [Important Notes](#important-notes)

---

## Project Purpose

The frontend provides the complete admin experience for managing grocery inventory. It is designed to be clean, responsive, easy to review, and connected to a Laravel REST API.

It enables admins to:

- Sign in and access protected dashboard pages
- Review stock metrics and recent inventory activity
- Manage inventory items
- Manage settings data used by dropdowns
- Work with loading, error, empty, success, and confirmation states

---

## Features

- **Protected Dashboard:** Auth guard redirects unauthenticated users to login.
- **Dashboard Metrics:** Loads API-driven inventory totals, stock value, recent items, and low-stock items.
- **Inventory CRUD:** List, filter, paginate, create, edit, and delete inventory items.
- **Settings CRUD:** Manage categories, subcategories, units, and suppliers.
- **Lookup Hooks:** Dropdowns are powered by database-backed lookup endpoints.
- **Central API Layer:** All paths, API actions, query keys, options, mutations, and hooks are separated by feature.
- **Global Axios Handling:** Token injection and 401 redirect logic are centralized.
- **Typed Contracts:** API responses, entities, filters, and payloads are typed.
- **Review-Friendly Checks:** Linting, type checking, unit tests, and production builds run locally and in GitHub Actions.

---

## Technologies Used

[![Next.js](https://img.shields.io/badge/Next.js-App_Router-black?logo=next.js)](https://nextjs.org/)
[![React](https://img.shields.io/badge/React-UI_Runtime-61DAFB?logo=react)](https://react.dev/)
[![TypeScript](https://img.shields.io/badge/TypeScript-Contracts-3178C6?logo=typescript)](https://www.typescriptlang.org/)
[![Axios](https://img.shields.io/badge/Axios-HTTP_Client-5A29E4)](https://axios-http.com/)
[![TanStack Query](https://img.shields.io/badge/TanStack_Query-Queries_and_Mutations-FF4154)](https://tanstack.com/query)
[![ESLint](https://img.shields.io/badge/ESLint-Code_Quality-4B32C3?logo=eslint)](https://eslint.org/)

---

## Project Structure

```txt
frontend/
|-- public/                         # Static assets
|-- src/
|   |-- app/                        # App Router pages and route groups
|   |   |-- (auth)/                 # Login route
|   |   `-- (admin)/                # Protected dashboard routes
|   |-- components/                 # UI and feature components
|   |   |-- auth/
|   |   |-- dashboard/
|   |   |-- inventory/
|   |   |-- layout/
|   |   |-- providers/
|   |   |-- settings/
|   |   `-- shared/
|   |-- constants/                  # Static UI constants
|   |-- hooks/                      # React Query hooks by feature
|   |-- lib/                        # API paths, Axios, actions, schemas, helpers
|   `-- types/                      # TypeScript API contracts
|-- package.json
|-- package-lock.json
|-- vitest.config.ts
`-- README.md
```

---

## Key Files

- `src/lib/api-paths.ts`: Central backend endpoint registry
- `src/lib/axios-instance.ts`: Shared Axios client and auth error handling
- `src/components/providers/app-providers.tsx`: Query and UI providers
- `src/hooks/inventory`: Inventory query and mutation hooks
- `src/hooks/lookups`: Database-backed dropdown hooks
- `src/lib/settings/actions`: Settings CRUD API actions and mutation options
- `src/types`: Shared API and UI contracts
- `src/test`: Vitest coverage for schemas, query keys, API helpers, and feature helpers

---

## Environment Variables

Create `.env` from `.env.example`:

```env
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
```

The backend must allow the frontend origin:

```txt
http://localhost:3000
```

Production API used by the deployed dashboard:

```env
NEXT_PUBLIC_API_BASE_URL=https://backend-test.hamzahamir.site/api
```

---

## Getting Started

### Prerequisites

- Node.js 20+
- Running Laravel API at `http://localhost:8000/api`

### Setup

```bash
cd frontend
npm install
npm run dev
```

Open:

```txt
http://localhost:3000
```

## Live Demo

```txt
https://test.hamzahamir.site
```

## Design Reference

| Asset        | URL                                                                                  |
| ------------ | ------------------------------------------------------------------------------------ |
| Figma design | https://www.figma.com/design/dnTSLh378IvJRopqkbx752/test?m=auto&t=wF3BqhssfDjsLKFZ-1 |

---

## Routes

| Route                     | Purpose                              |
| ------------------------- | ------------------------------------ |
| `/login`                  | Admin login                          |
| `/dashboard`              | Inventory dashboard overview         |
| `/inventory`              | Inventory table, filters, pagination |
| `/inventory/new`          | Create inventory item                |
| `/inventory/[id]/edit`    | Edit inventory item                  |
| `/settings/categories`    | Category CRUD                        |
| `/settings/subcategories` | Subcategory CRUD                     |
| `/settings/units`         | Unit CRUD                            |
| `/settings/suppliers`     | Supplier CRUD                        |

---

## API Integration

The frontend currently uses the canonical backend paths under `/api`. The backend also supports `/api/v1` aliases, but the frontend keeps the stable canonical base URL unless a versioned client migration is needed.

| Frontend area | API paths                                                                               |
| ------------- | --------------------------------------------------------------------------------------- |
| Auth          | `/auth/login`, `/auth/me`, `/auth/logout`                                               |
| Dashboard     | `/dashboard/stats`                                                                      |
| Inventory     | `/items`, `/items/{id}`                                                                 |
| Settings      | `/categories`, `/subcategories`, `/units`, `/suppliers`                                 |
| Lookups       | `/lookups/categories`, `/lookups/subcategories`, `/lookups/units`, `/lookups/suppliers` |

---

## Data Flow

```txt
Component
  -> src/hooks/{feature}
  -> src/lib/{feature}/actions
  -> src/lib/axios-instance.ts
  -> src/lib/api-paths.ts
  -> Laravel API
```

---

## Build and Checks

```bash
npm run lint
npm run typecheck
npm run test
npm run build
```

These commands are also executed by the repository GitHub Actions workflow before code is accepted into the deployment branch.

---

## CI/CD Quality Gate

The frontend participates in the repository workflow at `.github/workflows/ci.yml`. The frontend job installs dependencies with `npm ci`, runs ESLint, verifies TypeScript with `tsc --noEmit`, runs Vitest, and produces a production Next.js build.

Deployment is handled separately by Coolify. GitHub Actions is used as the quality gate that should pass before a pull request is merged into `main`.

---

## Important Notes

- Do not hardcode category, subcategory, unit, or supplier dropdown values.
- Use lookup hooks for business dropdowns.
- Use mutation hooks for create, update, and delete actions.
- Keep route files clean and compose feature components.
- Do not put raw API URLs in components.
- Keep endpoint changes centralized in `src/lib/api-paths.ts`.
