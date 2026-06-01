import type { LoginPayload } from "@/types";

export type ValidationResult<T> = {
  values: T;
  errors: Partial<Record<keyof T, string>>;
};

export function validateLogin(values: LoginPayload): ValidationResult<LoginPayload> {
  return {
    values,
    errors: {
      email: values.email.includes("@") ? undefined : "Enter a valid email address.",
      password: values.password.length >= 8 ? undefined : "Password must be at least 8 characters.",
    },
  };
}
