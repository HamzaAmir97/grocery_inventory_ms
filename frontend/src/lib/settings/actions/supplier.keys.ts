import type { SettingsListFilters } from "@/types";

export const supplierKeys = {
  all: ["settings", "suppliers"] as const,
  lists: () => [...supplierKeys.all, "list"] as const,
  list: (filters: SettingsListFilters = {}) => [...supplierKeys.lists(), filters] as const,
  details: () => [...supplierKeys.all, "detail"] as const,
  detail: (id: string | number) => [...supplierKeys.details(), String(id)] as const,
} as const;
