import { describe, expect, it } from "vitest";
import { supplierKeys } from "@/lib/settings/actions";

describe("supplierKeys", () => {
  it("builds stable supplier query keys", () => {
    const filters = { search: "north", page: 3 };

    expect(supplierKeys.all).toEqual(["settings", "suppliers"]);
    expect(supplierKeys.lists()).toEqual(["settings", "suppliers", "list"]);
    expect(supplierKeys.list(filters)).toEqual(["settings", "suppliers", "list", filters]);
    expect(supplierKeys.detail(9)).toEqual(["settings", "suppliers", "detail", "9"]);
  });
});
