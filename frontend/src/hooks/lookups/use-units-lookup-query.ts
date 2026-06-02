"use client";

import { useQuery } from "@tanstack/react-query";
import { unitsLookupQueryOptions } from "@/lib/lookups/actions";

export function useUnitsLookupQuery() {
  return useQuery(unitsLookupQueryOptions());
}
