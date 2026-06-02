import { queryOptions } from "@tanstack/react-query";
import type { SettingsListFilters } from "@/types";
import { getSupplier, getSuppliers } from "./supplier.api";
import { supplierKeys } from "./supplier.keys";

export function suppliersQueryOptions(filters: SettingsListFilters = {}) {
  return queryOptions({
    queryKey: supplierKeys.list(filters),
    queryFn: () => getSuppliers(filters),
  });
}

export function supplierQueryOptions(id: string | number) {
  return queryOptions({
    queryKey: supplierKeys.detail(id),
    queryFn: () => getSupplier(id),
    enabled: Boolean(id),
  });
}
