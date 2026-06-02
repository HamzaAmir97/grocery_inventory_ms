import type { ItemFilters, ItemFormValues } from "@/types";

export type InventorySortField = NonNullable<ItemFilters["sort_by"]>;

export const STEPS = ["Basic information", "Classification", "Pricing & stock", "Review & confirm"] as const;

export const EMPTY_VALUES: ItemFormValues = {
  name: "",
  sku: "",
  category_id: 0,
  subcategory_id: 0,
  unit_id: 0,
  supplier_id: 0,
  price: 0,
  stock_quantity: 0,
  low_stock_threshold: 10,
  description: "",
  is_active: true,
};
