import type { SettingsListFilters } from "@/types";

export const categoryKeys = {
  all: ["settings", "categories"] as const,
  lists: () => [...categoryKeys.all, "list"] as const,
  list: (filters: SettingsListFilters = {}) => [...categoryKeys.lists(), filters] as const,
  details: () => [...categoryKeys.all, "detail"] as const,
  detail: (id: string | number) => [...categoryKeys.details(), String(id)] as const,
} as const;
