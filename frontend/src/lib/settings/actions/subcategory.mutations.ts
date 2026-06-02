import type { QueryClient } from "@tanstack/react-query";
import type { SubcategoryPayload } from "@/types";
import { dashboardKeys } from "@/lib/dashboard/actions";
import { inventoryKeys } from "@/lib/inventory/actions";
import { lookupKeys } from "@/lib/lookups/actions";
import { createSubcategory, deleteSubcategory, updateSubcategory } from "./subcategory.api";
import { categoryKeys } from "./category.keys";
import { subcategoryKeys } from "./subcategory.keys";

export type UpdateSubcategoryVariables = {
  id: string | number;
  payload: SubcategoryPayload;
};

export function createSubcategoryMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: createSubcategory,
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: subcategoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: categoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.all });
    },
  };
}

export function updateSubcategoryMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: ({ id, payload }: UpdateSubcategoryVariables) => updateSubcategory(id, payload),
    onSuccess: (_response: unknown, variables: UpdateSubcategoryVariables) => {
      void queryClient.invalidateQueries({ queryKey: subcategoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: subcategoryKeys.detail(variables.id) });
      void queryClient.invalidateQueries({ queryKey: categoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.all });
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}

export function deleteSubcategoryMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: deleteSubcategory,
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: subcategoryKeys.all });
      void queryClient.invalidateQueries({ queryKey: categoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.all });
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}
