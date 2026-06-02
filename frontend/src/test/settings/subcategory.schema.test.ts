import { describe, expect, it } from "vitest";
import { validateSubcategory } from "@/lib/settings/schemas";

describe("validateSubcategory", () => {
  it("accepts a valid subcategory with a category relationship", () => {
    const result = validateSubcategory({
      name: "Leafy Greens",
      category_id: 1,
      description: "Spinach, lettuce, and similar items",
      is_active: true,
    });

    expect(result.errors.name).toBeUndefined();
    expect(result.errors.category_id).toBeUndefined();
  });

  it("requires a name and category id", () => {
    const result = validateSubcategory({
      name: " ",
      category_id: 0,
      description: null,
      is_active: true,
    });

    expect(result.errors.name).toBe("Subcategory name is required.");
    expect(result.errors.category_id).toBe("Category is required.");
  });
});
