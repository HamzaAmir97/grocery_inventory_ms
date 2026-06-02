"use client";

import { useQuery } from "@tanstack/react-query";
import { subcategoriesLookupQueryOptions } from "@/lib/lookups/actions";

export function useSubcategoriesLookupQuery(categoryId?: number) {
  return useQuery(subcategoriesLookupQueryOptions(categoryId));
}
