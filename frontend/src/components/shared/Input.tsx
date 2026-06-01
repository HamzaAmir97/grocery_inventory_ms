import { useId, type InputHTMLAttributes, type ReactNode } from "react";
import { Input as ShadcnInput } from "@/components/ui/input";
import { cn } from "@/lib/utils";
import { Field, type FieldProps } from "./Field";

export function Input({ label, error, helper, optional, icon, className = "", id, ...props }: InputHTMLAttributes<HTMLInputElement> & FieldProps & { icon?: ReactNode }) {
  const generatedId = useId();
  const inputId = id ?? generatedId;
  const control = (
    <ShadcnInput
      id={inputId}
      className={cn("input", error && "invalid", className)}
      aria-invalid={Boolean(error) || undefined}
      {...props}
    />
  );
  return (
    <Field label={label} error={error} helper={helper} optional={optional} htmlFor={inputId}>
      {icon ? (
        <span className="input-icon-wrap">
          {icon}
          {control}
        </span>
      ) : (
        control
      )}
    </Field>
  );
}
