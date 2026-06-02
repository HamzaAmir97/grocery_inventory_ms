import type { ItemFilters, ItemFormValues } from "@/types";

export type InventorySortField = NonNullable<ItemFilters["sort_by"]>;

export const STEP_META = [
  { label: "Basic information", shortLabel: "Basic Info", title: "Basic Information", subtitle: "Name & status" },
  { label: "Classification", shortLabel: "Classification", title: "Classification", subtitle: "Category & supplier" },
  { label: "Pricing & stock", shortLabel: "Pricing & Stock", title: "Pricing & Stock", subtitle: "Price & quantities" },
  { label: "Review & confirm", shortLabel: "Review", title: "Review & Confirm", subtitle: "Confirm & save" },
] as const;

export const STEPS = STEP_META.map((step) => step.label);

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
