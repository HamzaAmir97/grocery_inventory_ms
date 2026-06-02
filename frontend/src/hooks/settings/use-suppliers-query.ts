"use client";

import { useQuery } from "@tanstack/react-query";
import { suppliersQueryOptions } from "@/lib/settings/actions";
import type { SettingsListFilters } from "@/types";

export function useSuppliersQuery(filters: SettingsListFilters = {}) {
  return useQuery(suppliersQueryOptions(filters));
}
