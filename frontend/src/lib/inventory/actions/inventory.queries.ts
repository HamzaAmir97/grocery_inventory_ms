import { queryOptions } from "@tanstack/react-query";
import type { InventoryFilters } from "@/types";
import { getInventoryItem, getInventoryItems } from "./inventory.api";
import { inventoryKeys } from "./inventory.keys";

export function inventoryListQueryOptions(filters: InventoryFilters = {}) {
  return queryOptions({
    queryKey: inventoryKeys.list(filters),
    queryFn: () => getInventoryItems(filters),
  });
}

export function inventoryItemQueryOptions(id: string | number) {
  return queryOptions({
    queryKey: inventoryKeys.detail(id),
    queryFn: () => getInventoryItem(id),
    enabled: Boolean(id),
  });
}
