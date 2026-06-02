import type { SettingsListFilters } from "@/types";

export const unitKeys = {
  all: ["settings", "units"] as const,
  lists: () => [...unitKeys.all, "list"] as const,
  list: (filters: SettingsListFilters = {}) => [...unitKeys.lists(), filters] as const,
  details: () => [...unitKeys.all, "detail"] as const,
  detail: (id: string | number) => [...unitKeys.details(), String(id)] as const,
} as const;
