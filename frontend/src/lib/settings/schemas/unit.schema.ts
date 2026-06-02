import type { Unit } from "@/types";
import type { ValidationResult } from "@/lib/auth/schemas";

export function validateUnit(
  values: Pick<Unit, "name" | "symbol" | "description" | "is_active">,
): ValidationResult<Pick<Unit, "name" | "symbol" | "description" | "is_active">> {
  return {
    values,
    errors: {
      name: values.name.trim() ? undefined : "Unit name is required.",
      symbol: values.symbol?.trim() ? undefined : "Unit symbol is required.",
    },
  };
}
