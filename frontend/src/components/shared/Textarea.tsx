import { useId, type TextareaHTMLAttributes } from "react";
import { Textarea as ShadcnTextarea } from "@/components/ui/textarea";
import { cn } from "@/lib/utils";
import { Field, type FieldProps } from "./Field";

export function Textarea({ label, error, helper, optional, className = "", id, ...props }: TextareaHTMLAttributes<HTMLTextAreaElement> & FieldProps) {
  const generatedId = useId();
  const textareaId = id ?? generatedId;
  return (
    <Field label={label} error={error} helper={helper} optional={optional} htmlFor={textareaId}>
      <ShadcnTextarea
        id={textareaId}
        className={cn("textarea", error && "invalid", className)}
        aria-invalid={Boolean(error) || undefined}
        {...props}
      />
    </Field>
  );
}
