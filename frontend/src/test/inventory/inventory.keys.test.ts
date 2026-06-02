import { describe, expect, it } from "vitest";
import { inventoryKeys } from "@/lib/inventory/actions";

describe("inventoryKeys", () => {
  it("builds stable list and detail query keys", () => {
    const filters = { search: "rice", page: 2, low_stock: true };

    expect(inventoryKeys.all).toEqual(["inventory"]);
    expect(inventoryKeys.lists()).toEqual(["inventory", "list"]);
    expect(inventoryKeys.list(filters)).toEqual(["inventory", "list", filters]);
    expect(inventoryKeys.details()).toEqual(["inventory", "detail"]);
    expect(inventoryKeys.detail(15)).toEqual(["inventory", "detail", "15"]);
  });
});
