"use client";

import { useState } from "react";
import { DEFAULT_PAGE_SIZE } from "@/constants/ui";
import type { InventoryFilters } from "@/types";

const DEFAULT_INVENTORY_FILTERS: InventoryFilters = {
  page: 1,
  per_page: DEFAULT_PAGE_SIZE,
  sort_by: "created_at",
  sort_dir: "desc",
};

export function useInventoryFilters(initialFilters: InventoryFilters = DEFAULT_INVENTORY_FILTERS) {
  const [filters, setFilters] = useState<InventoryFilters>(initialFilters);

  function updateFilters(patch: Partial<InventoryFilters>) {
    setFilters((current) => ({ ...current, ...patch, page: 1 }));
  }

  function setPage(page: number) {
    setFilters((current) => ({ ...current, page }));
  }

  function resetFilters() {
    setFilters(DEFAULT_INVENTORY_FILTERS);
  }

  return {
    filters,
    setFilters,
    updateFilters,
    setPage,
    resetFilters,
  };
}
