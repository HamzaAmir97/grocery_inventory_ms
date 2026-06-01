import { describe, expect, it } from "vitest";
import { API_PATHS } from "@/lib";

describe("API_PATHS", () => {
  it("keeps inventory paths centralized and URL-encodes item ids", () => {
    expect(API_PATHS.INVENTORY.LIST).toBe("/items");
    expect(API_PATHS.INVENTORY.DETAIL("SKU/001")).toBe("/items/SKU%2F001");
    expect(API_PATHS.INVENTORY.UPDATE("rice bag")).toBe("/items/rice%20bag");
    expect(API_PATHS.INVENTORY.DELETE(42)).toBe("/items/42");
  });

  it("keeps lookup endpoints database-backed", () => {
    expect(API_PATHS.LOOKUPS).toEqual({
      CATEGORIES: "/lookups/categories",
      SUBCATEGORIES: "/lookups/subcategories",
      UNITS: "/lookups/units",
      SUPPLIERS: "/lookups/suppliers",
    });
  });

  it("URL-encodes settings entity ids", () => {
    expect(API_PATHS.SETTINGS.CATEGORIES.DETAIL("fresh/produce")).toBe("/categories/fresh%2Fproduce");
    expect(API_PATHS.SETTINGS.SUBCATEGORIES.UPDATE("leafy greens")).toBe("/subcategories/leafy%20greens");
    expect(API_PATHS.SETTINGS.UNITS.DELETE("kg/lb")).toBe("/units/kg%2Flb");
    expect(API_PATHS.SETTINGS.SUPPLIERS.DETAIL("north market")).toBe("/suppliers/north%20market");
  });
});
