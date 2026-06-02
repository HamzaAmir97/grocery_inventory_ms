import { describe, expect, it } from "vitest";
import { lookupKeys } from "@/lib/lookups/actions";

describe("lookupKeys", () => {
  it("builds stable lookup query keys", () => {
    expect(lookupKeys.all).toEqual(["lookups"]);
    expect(lookupKeys.categories()).toEqual(["lookups", "categories"]);
    expect(lookupKeys.subcategories()).toEqual(["lookups", "subcategories", "all"]);
    expect(lookupKeys.subcategories(3)).toEqual(["lookups", "subcategories", 3]);
    expect(lookupKeys.units()).toEqual(["lookups", "units"]);
    expect(lookupKeys.suppliers()).toEqual(["lookups", "suppliers"]);
  });
});
