import { describe, expect, it } from "vitest";
import { unitKeys } from "@/lib/settings/actions";

describe("unitKeys", () => {
  it("builds stable unit query keys", () => {
    const filters = { search: "kilo", status: "inactive" as const };

    expect(unitKeys.all).toEqual(["settings", "units"]);
    expect(unitKeys.lists()).toEqual(["settings", "units", "list"]);
    expect(unitKeys.list(filters)).toEqual(["settings", "units", "list", filters]);
    expect(unitKeys.detail(7)).toEqual(["settings", "units", "detail", "7"]);
  });
});
