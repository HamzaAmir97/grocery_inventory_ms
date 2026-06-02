import type { QueryClient } from "@tanstack/react-query";
import type { CategoryPayload } from "@/types";
import { dashboardKeys } from "@/lib/dashboard/actions";
import { inventoryKeys } from "@/lib/inventory/actions";
import { lookupKeys } from "@/lib/lookups/actions";
import { createCategory, deleteCategory, updateCategory } from "./category.api";
import { categoryKeys } from "./category.keys";
import { subcategoryKeys } from "./subcategory.keys";

export type UpdateCategoryVariables = {
  id: string | number;
  payload: CategoryPayload;
};

export function createCategoryMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: createCategory,
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: categoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.categories() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}

export function updateCategoryMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: ({ id, payload }: UpdateCategoryVariables) => updateCategory(id, payload),
    onSuccess: (_response: unknown, variables: UpdateCategoryVariables) => {
      void queryClient.invalidateQueries({ queryKey: categoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: categoryKeys.detail(variables.id) });
      void queryClient.invalidateQueries({ queryKey: subcategoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.categories() });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.all });
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}

export function deleteCategoryMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: deleteCategory,
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: categoryKeys.all });
      void queryClient.invalidateQueries({ queryKey: subcategoryKeys.all });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.all });
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}
