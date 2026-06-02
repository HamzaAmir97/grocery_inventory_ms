import type { SettingsListFilters } from "@/types";

export const subcategoryKeys = {
  all: ["settings", "subcategories"] as const,
  lists: () => [...subcategoryKeys.all, "list"] as const,
  list: (filters: SettingsListFilters = {}) => [...subcategoryKeys.lists(), filters] as const,
  details: () => [...subcategoryKeys.all, "detail"] as const,
  detail: (id: string | number) => [...subcategoryKeys.details(), String(id)] as const,
} as const;
