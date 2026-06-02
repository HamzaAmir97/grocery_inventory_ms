import type { Supplier } from "@/types";
import type { ValidationResult } from "@/lib/auth/schemas";

export function validateSupplier(
  values: Pick<Supplier, "name" | "email" | "phone" | "description" | "is_active">,
): ValidationResult<Pick<Supplier, "name" | "email" | "phone" | "description" | "is_active">> {
  return {
    values,
    errors: {
      name: values.name.trim() ? undefined : "Supplier name is required.",
      email: values.email && !values.email.includes("@") ? "Enter a valid email address." : undefined,
    },
  };
}
