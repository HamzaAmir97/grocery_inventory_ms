import { queryOptions } from "@tanstack/react-query";
import type { SettingsListFilters } from "@/types";
import { getCategories, getCategory } from "./category.api";
import { categoryKeys } from "./category.keys";

export function categoriesQueryOptions(filters: SettingsListFilters = {}) {
  return queryOptions({
    queryKey: categoryKeys.list(filters),
    queryFn: () => getCategories(filters),
  });
}

export function categoryQueryOptions(id: string | number) {
  return queryOptions({
    queryKey: categoryKeys.detail(id),
    queryFn: () => getCategory(id),
    enabled: Boolean(id),
  });
}
