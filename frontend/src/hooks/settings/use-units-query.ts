"use client";

import { useQuery } from "@tanstack/react-query";
import { unitsQueryOptions } from "@/lib/settings/actions";
import type { SettingsListFilters } from "@/types";

export function useUnitsQuery(filters: SettingsListFilters = {}) {
  return useQuery(unitsQueryOptions(filters));
}
