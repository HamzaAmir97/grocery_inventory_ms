import type { Subcategory } from "@/types";
import type { ValidationResult } from "@/lib/auth/schemas";

export function validateSubcategory(
  values: Pick<Subcategory, "name" | "category_id" | "description" | "is_active">,
): ValidationResult<Pick<Subcategory, "name" | "category_id" | "description" | "is_active">> {
  return {
    values,
    errors: {
      name: values.name.trim() ? undefined : "Subcategory name is required.",
      category_id: values.category_id > 0 ? undefined : "Category is required.",
    },
  };
}
