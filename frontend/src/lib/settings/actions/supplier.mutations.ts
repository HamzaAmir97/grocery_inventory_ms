import type { QueryClient } from "@tanstack/react-query";
import type { SupplierPayload } from "@/types";
import { dashboardKeys } from "@/lib/dashboard/actions";
import { inventoryKeys } from "@/lib/inventory/actions";
import { lookupKeys } from "@/lib/lookups/actions";
import { createSupplier, deleteSupplier, updateSupplier } from "./supplier.api";
import { supplierKeys } from "./supplier.keys";

export type UpdateSupplierVariables = {
  id: string | number;
  payload: SupplierPayload;
};

export function createSupplierMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: createSupplier,
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: supplierKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.suppliers() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}

export function updateSupplierMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: ({ id, payload }: UpdateSupplierVariables) => updateSupplier(id, payload),
    onSuccess: (_response: unknown, variables: UpdateSupplierVariables) => {
      void queryClient.invalidateQueries({ queryKey: supplierKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: supplierKeys.detail(variables.id) });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.suppliers() });
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}

export function deleteSupplierMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: deleteSupplier,
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: supplierKeys.all });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.suppliers() });
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}
