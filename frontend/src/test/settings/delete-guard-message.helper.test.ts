import { describe, expect, it } from "vitest";
import { getDeleteGuardMessage } from "@/lib/settings/helpers";

describe("getDeleteGuardMessage", () => {
  it("builds a clear blocked-delete explanation", () => {
    expect(
      getDeleteGuardMessage({
        id: 1,
        name: "Fresh Produce",
        is_active: true,
      }),
    ).toBe(
      "\"Fresh Produce\" is currently used by inventory items or related records. Reassign or remove those first, then try again.",
    );
  });
});
