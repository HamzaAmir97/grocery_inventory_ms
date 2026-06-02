"use client";

import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useEffect, useRef, useState } from "react";
import { ROUTES } from "@/constants";
import { DEFAULT_PAGE_SIZE } from "@/constants/ui";
import {
  Button,
  ConfirmDialog,
  EmptyState,
  ErrorAlert,
  IconBox,
  IconPlus,
  PageHeader,
  Pagination,
  useToast,
} from "@/components/shared";
import {
  useDeleteItemMutation,
  useInventoryFilters,
  useInventoryQuery,
} from "@/hooks/inventory";
import {
  useCategoriesLookupQuery,
  useSuppliersLookupQuery,
} from "@/hooks/lookups";
import { formatNumber } from "@/lib/format";
import type { Item } from "@/types";
import { InventoryTable } from "./InventoryTable";
import { InventoryTableSkeleton } from "./InventoryTableSkeleton";
import { InventoryToolbar } from "./InventoryToolbar";

export function InventoryPageContent() {
  const { notify } = useToast();
  const searchParams = useSearchParams();
  const routeSearch = searchParams.get("search")?.trim() ?? "";
  const lastRouteSearchRef = useRef<string | null>(null);
  const { filters, setFilters, updateFilters, setPage } = useInventoryFilters();
  const inventoryQuery = useInventoryQuery(filters);
  const categoriesQuery = useCategoriesLookupQuery();
  const suppliersQuery = useSuppliersLookupQuery();
  const deleteItemMutation = useDeleteItemMutation();
  const [pendingDelete, setPendingDelete] = useState<Item | null>(null);
  const items = inventoryQuery.data?.data ?? [];
  const meta = inventoryQuery.data?.meta ?? { current_page: 1, per_page: DEFAULT_PAGE_SIZE, total: 0, last_page: 1 };
  const categories = categoriesQuery.data?.data ?? [];
  const suppliers = suppliersQuery.data?.data ?? [];
  const error = inventoryQuery.error instanceof Error ? inventoryQuery.error.message : "";

  useEffect(() => {
    if (lastRouteSearchRef.current === routeSearch) return;

    lastRouteSearchRef.current = routeSearch;
    setFilters((current) => ({ ...current, search: routeSearch || undefined, page: 1 }));
  }, [routeSearch, setFilters]);

  async function confirmDelete() {
    if (!pendingDelete) return;
    try {
      await deleteItemMutation.mutateAsync(pendingDelete.id);
      notify({ kind: "success", title: "Item deleted" });
      setPendingDelete(null);
    } catch (deleteError) {
      notify({ kind: "error", title: "Could not delete item", message: deleteError instanceof Error ? deleteError.message : undefined });
    }
  }

  return (
    <>
      <div style={{ display: "grid", gap: 20 }}>
        <PageHeader
          title="Inventory"
          description={`${formatNumber(meta.total)} items`}
          action={
            <Link href={ROUTES.inventoryNew}>
              <Button icon={<IconPlus size={16} />}>Add item</Button>
            </Link>
          }
        />

        <InventoryToolbar filters={filters} categories={categories} suppliers={suppliers} onChange={updateFilters} />

        {inventoryQuery.isLoading ? <InventoryTableSkeleton rows={6} /> : null}
        {error ? <ErrorAlert message={error} /> : null}

        {!inventoryQuery.isLoading && !error ? (
          items.length === 0 ? (
            <EmptyState
              icon={<IconBox size={24} />}
              title="No items found"
              message="No items match your filters yet — add your first item to get started."
              action={
                <Link href={ROUTES.inventoryNew}>
                  <Button icon={<IconPlus size={16} />}>Add item</Button>
                </Link>
              }
            />
          ) : (
            <>
              <InventoryTable
                filters={filters}
                items={items}
                onDelete={setPendingDelete}
                onSort={updateFilters}
              />
              <Pagination
                page={meta.current_page}
                lastPage={meta.last_page}
                total={meta.total}
                perPage={meta.per_page}
                onChange={setPage}
              />
            </>
          )
        ) : null}
      </div>

      <ConfirmDialog
        open={Boolean(pendingDelete)}
        onClose={() => setPendingDelete(null)}
        title="Delete item?"
        message={
          pendingDelete
            ? `Delete "${pendingDelete.name}"? This permanently removes the item from inventory.`
            : ""
        }
        confirmLabel="Delete item"
        busy={deleteItemMutation.isPending}
        onConfirm={confirmDelete}
      />
    </>
  );
}
