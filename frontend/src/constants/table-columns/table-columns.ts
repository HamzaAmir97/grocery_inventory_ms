export const INVENTORY_COLUMNS = ["name", "sku", "category", "supplier", "stock", "price", "status"] as const;

export const SETTINGS_COLUMNS = ["name", "description", "status", "actions"] as const;

export const ITEM_SORT_OPTIONS = [
  { label: "Newest", value: "created_at" },
  { label: "Name", value: "name" },
  { label: "SKU", value: "sku" },
  { label: "Category", value: "category" },
  { label: "Subcategory", value: "subcategory" },
  { label: "Unit", value: "unit" },
  { label: "Supplier", value: "supplier" },
  { label: "Price", value: "price" },
  { label: "Stock", value: "stock_quantity" },
] as const;
