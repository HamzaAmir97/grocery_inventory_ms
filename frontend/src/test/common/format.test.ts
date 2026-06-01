import { describe, expect, it } from "vitest";
import { formatCurrency, formatNumber, initials, isLowStock } from "@/lib/format";

describe("format helpers", () => {
  it("formats dashboard numbers and currency consistently", () => {
    expect(formatNumber(1240)).toBe("1,240");
    expect(formatCurrency(3.5)).toBe("$3.50");
  });

  it("derives initials from the first two non-empty words", () => {
    expect(initials("Fresh Produce Market")).toBe("FP");
    expect(initials("  rice  ")).toBe("R");
    expect(initials("")).toBe("");
  });

  it("flags stock as low when quantity is less than or equal to threshold", () => {
    expect(isLowStock(5, 5)).toBe(true);
    expect(isLowStock(4, 5)).toBe(true);
    expect(isLowStock(6, 5)).toBe(false);
  });
});
