"use client";

import { useQuery } from "@tanstack/react-query";
import { categoriesLookupQueryOptions } from "@/lib/lookups/actions";

export function useCategoriesLookupQuery() {
  return useQuery(categoriesLookupQueryOptions());
}
