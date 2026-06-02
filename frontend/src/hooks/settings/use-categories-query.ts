"use client";

import { useQuery } from "@tanstack/react-query";
import { categoriesQueryOptions } from "@/lib/settings/actions";
import type { SettingsListFilters } from "@/types";

export function useCategoriesQuery(filters: SettingsListFilters = {}) {
  return useQuery(categoriesQueryOptions(filters));
}
