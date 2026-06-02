"use client";

import { useQuery } from "@tanstack/react-query";
import { inventoryListQueryOptions } from "@/lib/inventory/actions";
import type { InventoryFilters } from "@/types";

export function useInventoryQuery(filters: InventoryFilters = {}) {
  return useQuery(inventoryListQueryOptions(filters));
}
