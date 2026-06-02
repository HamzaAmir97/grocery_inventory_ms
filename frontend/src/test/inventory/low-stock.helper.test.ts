import { describe, expect, it } from "vitest";
import { isLowStock } from "@/lib/inventory/helpers";

describe("isLowStock", () => {
  it("matches the project low-stock rule", () => {
    expect(isLowStock(8, 10)).toBe(true);
    expect(isLowStock(10, 10)).toBe(true);
    expect(isLowStock(11, 10)).toBe(false);
  });
});
