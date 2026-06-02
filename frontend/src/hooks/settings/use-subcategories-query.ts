"use client";

import { useQuery } from "@tanstack/react-query";
import { subcategoriesQueryOptions } from "@/lib/settings/actions";
import type { SettingsListFilters } from "@/types";

export function useSubcategoriesQuery(filters: SettingsListFilters = {}) {
  return useQuery(subcategoriesQueryOptions(filters));
}
