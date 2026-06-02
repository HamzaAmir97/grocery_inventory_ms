import type { QueryClient } from "@tanstack/react-query";
import type { UnitPayload } from "@/types";
import { dashboardKeys } from "@/lib/dashboard/actions";
import { inventoryKeys } from "@/lib/inventory/actions";
import { lookupKeys } from "@/lib/lookups/actions";
import { createUnit, deleteUnit, updateUnit } from "./unit.api";
import { unitKeys } from "./unit.keys";

export type UpdateUnitVariables = {
  id: string | number;
  payload: UnitPayload;
};

export function createUnitMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: createUnit,
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: unitKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.units() });
    },
  };
}

export function updateUnitMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: ({ id, payload }: UpdateUnitVariables) => updateUnit(id, payload),
    onSuccess: (_response: unknown, variables: UpdateUnitVariables) => {
      void queryClient.invalidateQueries({ queryKey: unitKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: unitKeys.detail(variables.id) });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.units() });
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}

export function deleteUnitMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: deleteUnit,
    onSuccess: () => {
      void queryClient.invalidateQueries({ queryKey: unitKeys.all });
      void queryClient.invalidateQueries({ queryKey: lookupKeys.units() });
      void queryClient.invalidateQueries({ queryKey: inventoryKeys.lists() });
      void queryClient.invalidateQueries({ queryKey: dashboardKeys.all });
    },
  };
}
