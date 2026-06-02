import type { QueryClient } from "@tanstack/react-query";
import type { CreateItemPayload, UpdateItemPayload } from "@/types";
import { createInventoryItem, deleteInventoryItem, updateInventoryItem } from "./inventory.api";
import { dashboardKeys } from "@/lib/dashboard/actions";
import { inventoryKeys } from "./inventory.keys";

export type UpdateInventoryItemVariables = {
  id: string | number;
  payload: UpdateItemPayload;
};

export function createInventoryItemMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: (payload: CreateItemPayload) => createInventoryItem(payload),
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}

export function updateInventoryItemMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: ({ id, payload }: UpdateInventoryItemVariables) => updateInventoryItem(id, payload),
    onSuccess: (_response: unknown, variables: UpdateInventoryItemVariables) => {
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.detail(variables.id) });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}

export function deleteInventoryItemMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: deleteInventoryItem,
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.all });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}
