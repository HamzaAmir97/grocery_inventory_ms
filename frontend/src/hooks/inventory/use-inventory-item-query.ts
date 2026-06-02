"use client";

import { useQuery } from "@tanstack/react-query";
import { inventoryItemQueryOptions } from "@/lib/inventory/actions";

export function useInventoryItemQuery(id: string | number) {
  return useQuery(inventoryItemQueryOptions(id));
}
