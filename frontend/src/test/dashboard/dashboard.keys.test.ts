import { describe, expect, it } from "vitest";
import { dashboardKeys } from "@/lib/dashboard/actions";

describe("dashboardKeys", () => {
  it("builds stable dashboard query keys", () => {
    expect(dashboardKeys.all).toEqual(["dashboard"]);
    expect(dashboardKeys.stats()).toEqual(["dashboard", "stats"]);
  });
});
