import { describe, expect, it } from "vitest";
import { validateSupplier } from "@/lib/settings/schemas";

describe("validateSupplier", () => {
  it("accepts a supplier with an optional valid email", () => {
    const result = validateSupplier({
      name: "North Market",
      email: "contact@north.example",
      phone: "555-0100",
      description: "Local grocery supplier",
      is_active: true,
    });

    expect(result.errors.name).toBeUndefined();
    expect(result.errors.email).toBeUndefined();
  });

  it("requires a name and validates email shape when provided", () => {
    const result = validateSupplier({
      name: " ",
      email: "contact.north.example",
      phone: "",
      description: null,
      is_active: true,
    });

    expect(result.errors.name).toBe("Supplier name is required.");
    expect(result.errors.email).toBe("Enter a valid email address.");
  });
});
