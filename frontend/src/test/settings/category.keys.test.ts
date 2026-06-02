import { describe, expect, it } from "vitest";
import { categoryKeys } from "@/lib/settings/actions";

describe("categoryKeys", () => {
  it("builds stable category query keys", () => {
    const filters = { search: "fresh", status: "active" as const, page: 2 };

    expect(categoryKeys.all).toEqual(["settings", "categories"]);
    expect(categoryKeys.lists()).toEqual(["settings", "categories", "list"]);
    expect(categoryKeys.list(filters)).toEqual(["settings", "categories", "list", filters]);
    expect(categoryKeys.detail(5)).toEqual(["settings", "categories", "detail", "5"]);
  });
});
