import { describe, expect, it } from "vitest";
import { subcategoryKeys } from "@/lib/settings/actions";

describe("subcategoryKeys", () => {
  it("builds stable subcategory query keys", () => {
    const filters = { category_id: 3, search: "leafy" };

    expect(subcategoryKeys.all).toEqual(["settings", "subcategories"]);
    expect(subcategoryKeys.lists()).toEqual(["settings", "subcategories", "list"]);
    expect(subcategoryKeys.list(filters)).toEqual(["settings", "subcategories", "list", filters]);
    expect(subcategoryKeys.detail("leafy/3")).toEqual(["settings", "subcategories", "detail", "leafy/3"]);
  });
});
