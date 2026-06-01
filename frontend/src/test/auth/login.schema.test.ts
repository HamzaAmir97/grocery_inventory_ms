import { describe, expect, it } from "vitest";
import { validateLogin } from "@/lib/auth/schemas";

describe("validateLogin", () => {
  it("accepts a valid admin login payload", () => {
    const result = validateLogin({
      email: "admin@example.com",
      password: "password123",
    });

    expect(result.errors.email).toBeUndefined();
    expect(result.errors.password).toBeUndefined();
  });

  it("rejects malformed email and short password values", () => {
    const result = validateLogin({
      email: "admin.example.com",
      password: "short",
    });

    expect(result.errors.email).toBe("Enter a valid email address.");
    expect(result.errors.password).toBe("Password must be at least 8 characters.");
  });
});
