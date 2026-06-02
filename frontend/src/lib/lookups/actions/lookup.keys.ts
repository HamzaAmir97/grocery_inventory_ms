export const lookupKeys = {
  all: ["lookups"] as const,
  categories: () => [...lookupKeys.all, "categories"] as const,
  subcategories: (categoryId?: number) => [...lookupKeys.all, "subcategories", categoryId ?? "all"] as const,
  units: () => [...lookupKeys.all, "units"] as const,
  suppliers: () => [...lookupKeys.all, "suppliers"] as const,
} as const;
