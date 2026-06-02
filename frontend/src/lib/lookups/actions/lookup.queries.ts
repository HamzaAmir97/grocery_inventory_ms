import { queryOptions } from "@tanstack/react-query";
import { getCategoryOptions, getSubcategoryOptions, getSupplierOptions, getUnitOptions } from "./lookup.api";
import { lookupKeys } from "./lookup.keys";

export function categoriesLookupQueryOptions() {
  return queryOptions({
    queryKey: lookupKeys.categories(),
    queryFn: getCategoryOptions,
  });
}

export function subcategoriesLookupQueryOptions(categoryId?: number) {
  return queryOptions({
    queryKey: lookupKeys.subcategories(categoryId),
    queryFn: () => getSubcategoryOptions(categoryId),
    enabled: categoryId === undefined || categoryId > 0,
  });
}

export function unitsLookupQueryOptions() {
  return queryOptions({
    queryKey: lookupKeys.units(),
    queryFn: getUnitOptions,
  });
}

export function suppliersLookupQueryOptions() {
  return queryOptions({
    queryKey: lookupKeys.suppliers(),
    queryFn: getSupplierOptions,
  });
}
