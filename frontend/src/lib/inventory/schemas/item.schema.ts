import type { ItemFormValues } from "@/types";
import type { ValidationResult } from "@/lib/auth/schemas";

export function validateItem(values: ItemFormValues): ValidationResult<ItemFormValues> {
  return {
    values,
    errors: {
      name: values.name.trim() ? undefined : "Item name is required.",
      sku: values.sku.trim() ? undefined : "SKU is required.",
      category_id: values.category_id > 0 ? undefined : "Category is required.",
      subcategory_id: values.subcategory_id > 0 ? undefined : "Subcategory is required.",
      unit_id: values.unit_id > 0 ? undefined : "Unit is required.",
      supplier_id: values.supplier_id > 0 ? undefined : "Supplier is required.",
      price: values.price >= 0 ? undefined : "Price cannot be negative.",
      stock_quantity: values.stock_quantity >= 0 ? undefined : "Stock cannot be negative.",
      low_stock_threshold:
        values.low_stock_threshold >= 0 ? undefined : "Low stock threshold cannot be negative.",
    },
  };
}
