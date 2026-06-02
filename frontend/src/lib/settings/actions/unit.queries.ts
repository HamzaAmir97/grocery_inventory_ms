import { queryOptions } from "@tanstack/react-query";
import type { SettingsListFilters } from "@/types";
import { getUnit, getUnits } from "./unit.api";
import { unitKeys } from "./unit.keys";

export function unitsQueryOptions(filters: SettingsListFilters = {}) {
  return queryOptions({
    queryKey: unitKeys.list(filters),
    queryFn: () => getUnits(filters),
  });
}

export function unitQueryOptions(id: string | number) {
  return queryOptions({
    queryKey: unitKeys.detail(id),
    queryFn: () => getUnit(id),
    enabled: Boolean(id),
  });
}
