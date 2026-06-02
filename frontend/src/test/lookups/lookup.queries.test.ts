import { describe, expect, it } from "vitest";
import {
  categoriesLookupQueryOptions,
  subcategoriesLookupQueryOptions,
  suppliersLookupQueryOptions,
  unitsLookupQueryOptions,
} from "@/lib/lookups/actions";
import { lookupKeys } from "@/lib/lookups/actions";

describe("lookup query options", () => {
  it("uses database-backed lookup keys", () => {
    expect(categoriesLookupQueryOptions().queryKey).toEqual(lookupKeys.categories());
    expect(unitsLookupQueryOptions().queryKey).toEqual(lookupKeys.units());
    expect(suppliersLookupQueryOptions().queryKey).toEqual(lookupKeys.suppliers());
  });

  it("disables subcategory lookup for an invalid category id", () => {
    expect(subcategoriesLookupQueryOptions().enabled).toBe(true);
    expect(subcategoriesLookupQueryOptions(4).enabled).toBe(true);
    expect(subcategoriesLookupQueryOptions(0).enabled).toBe(false);
  });
});
