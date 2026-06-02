import { queryOptions } from "@tanstack/react-query";
import type { SettingsListFilters } from "@/types";
import { getSubcategories, getSubcategory } from "./subcategory.api";
import { subcategoryKeys } from "./subcategory.keys";

export function subcategoriesQueryOptions(filters: SettingsListFilters = {}) {
  return queryOptions({
    queryKey: subcategoryKeys.list(filters),
    queryFn: () => getSubcategories(filters),
  });
}

export function subcategoryQueryOptions(id: string | number) {
  return queryOptions({
    queryKey: subcategoryKeys.detail(id),
    queryFn: () => getSubcategory(id),
    enabled: Boolean(id),
  });
}
