import { describe, expect, it } from "vitest";
import { validateUnit } from "@/lib/settings/schemas";

describe("validateUnit", () => {
  it("accepts a valid unit name and symbol", () => {
    const result = validateUnit({
      name: "Kilogram",
      symbol: "kg",
      description: "Weight unit",
      is_active: true,
    });

    expect(result.errors.name).toBeUndefined();
    expect(result.errors.symbol).toBeUndefined();
  });

  it("requires both unit name and symbol", () => {
    const result = validateUnit({
      name: " ",
      symbol: " ",
      description: null,
      is_active: true,
    });

    expect(result.errors.name).toBe("Unit name is required.");
    expect(result.errors.symbol).toBe("Unit symbol is required.");
  });
});
