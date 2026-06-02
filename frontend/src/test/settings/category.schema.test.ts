import { describe, expect, it } from "vitest";
import { validateCategory } from "@/lib/settings/schemas";

describe("validateCategory", () => {
  it("accepts a valid category", () => {
    const result = validateCategory({
      name: "Fresh Produce",
      description: "Fruit and vegetables",
      is_active: true,
    });

    expect(result.errors.name).toBeUndefined();
  });

  it("requires a non-empty category name", () => {
    const result = validateCategory({
      name: " ",
      description: null,
      is_active: true,
    });

    expect(result.errors.name).toBe("Category name is required.");
  });
});
