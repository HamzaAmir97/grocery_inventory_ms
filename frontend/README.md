<p align="right">
  <a href="../README.md"><strong>Back to Home</strong></a>
  &nbsp;|&nbsp;
  <a href="../backend/grocery-inventory-backend/README.md"><strong>Backend API</strong></a>
</p>

<div align="center">

# Grocery Inventory Frontend

The Next.js admin dashboard for the Grocery Inventory Management System.

[![Next.js](https://img.shields.io/badge/Next.js-16-black?logo=next.js)](https://nextjs.org/)
[![React](https://img.shields.io/badge/React-19-61DAFB?logo=react)](https://react.dev/)
[![TypeScript](https://img.shields.io/badge/TypeScript-Strict-3178C6?logo=typescript)](https://www.typescriptlang.org/)
[![TanStack Query](https://img.shields.io/badge/TanStack_Query-Server_State-FF4154)](https://tanstack.com/query)
[![Axios](https://img.shields.io/badge/Axios-API_Client-5A29E4)](https://axios-http.com/)
[![CI Quality Gate](https://github.com/HamzaAmir97/inventory_managment_system/actions/workflows/ci.yml/badge.svg)](https://github.com/HamzaAmir97/inventory_managment_system/actions/workflows/ci.yml)

</div>

![Dashboard overview](./public/readme/dashboard-overview.png)

## Overview

The frontend provides a focused admin workspace for tracking grocery stock, reviewing dashboard metrics, managing inventory records, and maintaining database-backed settings. It uses the Next.js App Router, typed API contracts, TanStack Query for server state, and a centralized Axios client for authenticated backend communication.

## Feature Highlights

- Protected login flow and authenticated dashboard layout.
- Dashboard cards for total items, categories, suppliers, and low-stock status.
- Inventory growth chart and category distribution summary.
- Inventory table with filtering, pagination, create, edit, and delete flows.
- Multi-step inventory form with database-backed category, subcategory, unit, and supplier dropdowns.
- Settings screens for categories, subcategories, units, and suppliers.
- Centralized API path registry so endpoint changes are managed in one place.
- Loading, empty, error, success, and confirmation states across the admin surface.

## Project Structure

```txt
frontend/
|-- public/
|   `-- readme/                    # README screenshots and visual assets
|-- src/
|   |-- app/
|   |   |-- (auth)/                # Login route
|   |   `-- (admin)/               # Protected dashboard routes
|   |-- components/
|   |   |-- auth/
|   |   |-- dashboard/
|   |   |-- inventory/
|   |   |-- layout/
|   |   |-- providers/
|   |   |-- settings/
|   |   `-- shared/
|   |-- constants/
|   |-- hooks/
|   |-- lib/
|   `-- types/
|-- package.json
|-- package-lock.json
`-- README.md
```

## Data Flow

```txt
Page / Component
  -> src/hooks/{feature}
  -> src/lib/{feature}/actions
  -> src/lib/axios-instance.ts
  -> src/lib/api-paths.ts
  -> Laravel API
```

## Key Files

| File | Purpose |
| --- | --- |
| `src/lib/api-paths.ts` | Central endpoint registry |
| `src/lib/axios-instance.ts` | Shared Axios client, token injection, and 401 handling |
| `src/components/providers/app-providers.tsx` | App-level providers |
| `src/hooks/inventory` | Inventory queries and mutations |
| `src/hooks/lookups` | Database-backed dropdown data |
| `src/lib/settings/actions` | Settings API actions and mutation options |
| `src/types` | Shared API and UI contracts |

## Environment

Create `.env` from `.env.example`:

```env
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000/api
```

The backend must allow the frontend origin:

```txt
http://localhost:3000
```

## Getting Started

```bash
cd frontend
npm install
npm run dev
```

Open:

```txt
http://localhost:3000
```

## Routes

| Route | Purpose |
| --- | --- |
| `/login` | Admin login |
| `/dashboard` | Inventory dashboard overview |
| `/inventory` | Inventory table, filters, and pagination |
| `/inventory/new` | Create inventory item |
| `/inventory/[id]/edit` | Edit inventory item |
| `/settings/categories` | Category management |
| `/settings/subcategories` | Subcategory management |
| `/settings/units` | Unit management |
| `/settings/suppliers` | Supplier management |

## API Integration

| Frontend area | API paths |
| --- | --- |
| Auth | `/auth/login`, `/auth/me`, `/auth/logout`, `/auth/refresh` |
| Dashboard | `/dashboard/stats` |
| Inventory | `/items`, `/items/{id}`, `/items/{id}/movements` |
| Settings | `/categories`, `/subcategories`, `/units`, `/suppliers` |
| Lookups | `/lookups/categories`, `/lookups/subcategories`, `/lookups/units`, `/lookups/suppliers` |

## Validation Commands

```bash
npm run lint
npm run typecheck
npm run test
npm run build
```

## Notes

- Keep business dropdown values database-driven through lookup hooks.
- Keep raw URLs out of UI components.
- Update `src/lib/api-paths.ts` when backend endpoint paths change.
- Use feature hooks and mutation helpers instead of calling Axios directly from components.
