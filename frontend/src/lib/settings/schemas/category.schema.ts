import type { Category } from "@/types";
import type { ValidationResult } from "@/lib/auth/schemas";

export function validateCategory(
  values: Pick<Category, "name" | "description" | "is_active">,
): ValidationResult<Pick<Category, "name" | "description" | "is_active">> {
  return {
    values,
    errors: {
      name: values.name.trim() ? undefined : "Category name is required.",
    },
  };
}
