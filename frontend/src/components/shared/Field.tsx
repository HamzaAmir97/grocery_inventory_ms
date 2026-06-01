import type { ReactNode } from "react";
import { Label as ShadcnLabel } from "@/components/ui/label";

export type FieldProps = { label?: string; error?: string; helper?: string; optional?: boolean };

export function Field({ label, error, helper, optional, children, htmlFor }: FieldProps & { children: ReactNode; htmlFor?: string }) {
  return (
    <div className="field">
      {label ? (
        <ShadcnLabel className="field-label" htmlFor={htmlFor}>
          {label}
          {optional ? <span className="optional">· optional</span> : null}
        </ShadcnLabel>
      ) : null}
      {children}
      {error ? <span className="field-error">{error}</span> : helper ? <span className="field-helper">{helper}</span> : null}
    </div>
  );
}
