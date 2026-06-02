import { describe, expect, it } from "vitest";
import { validateItem } from "@/lib/inventory/schemas";
import type { ItemFormValues } from "@/types";

const validItemValues: ItemFormValues = {
  name: "Basmati Rice",
  sku: "GR-RICE-001",
  category_id: 1,
  subcategory_id: 2,
  unit_id: 3,
  supplier_id: 4,
  price: 12.5,
  stock_quantity: 20,
  low_stock_threshold: 5,
  description: "Premium rice",
  is_active: true,
};

describe("validateItem", () => {
  it("accepts valid item form values", () => {
    const result = validateItem(validItemValues);

    expect(Object.values(result.errors).filter(Boolean)).toEqual([]);
  });

  it("requires item identity and database-backed lookup ids", () => {
    const result = validateItem({
      ...validItemValues,
      name: " ",
      sku: " ",
      category_id: 0,
      subcategory_id: 0,
      unit_id: 0,
      supplier_id: 0,
    });

    expect(result.errors.name).toBe("Item name is required.");
    expect(result.errors.sku).toBe("SKU is required.");
    expect(result.errors.category_id).toBe("Category is required.");
    expect(result.errors.subcategory_id).toBe("Subcategory is required.");
    expect(result.errors.unit_id).toBe("Unit is required.");
    expect(result.errors.supplier_id).toBe("Supplier is required.");
  });

  it("rejects negative price and stock values", () => {
    const result = validateItem({
      ...validItemValues,
      price: -1,
      stock_quantity: -2,
      low_stock_threshold: -3,
    });

    expect(result.errors.price).toBe("Price cannot be negative.");
    expect(result.errors.stock_quantity).toBe("Stock cannot be negative.");
    expect(result.errors.low_stock_threshold).toBe("Low stock threshold cannot be negative.");
  });
});
