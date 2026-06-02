"use client";

import { useQuery } from "@tanstack/react-query";
import { suppliersLookupQueryOptions } from "@/lib/lookups/actions";

export function useSuppliersLookupQuery() {
  return useQuery(suppliersLookupQueryOptions());
}
