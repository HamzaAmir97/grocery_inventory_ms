import type { ItemFormValues } from "@/types";
import { STEPS } from "./helpers";

export function Stepper({ step, errors }: { step: number; errors: Partial<Record<keyof ItemFormValues, string>> }) {
  const hasErrors = Object.keys(errors).length > 0;
  return (
    <div className="stepper">
      {STEPS.map((label, index) => {
        const state = index < step ? "done" : index === step ? (hasErrors ? "error" : "active") : "todo";
        return (
          <div key={label} className="step" style={{ flex: index < STEPS.length - 1 ? 1 : "0 0 auto" }}>
            <span className={`step-circle ${state}`}>{state === "done" ? "✓" : index + 1}</span>
            <span className={`step-label ${index === step ? "active" : index > step ? "todo" : ""} hidden md:inline`}>{label}</span>
            {index < STEPS.length - 1 ? <span className={`step-line ${index < step ? "done" : ""}`} /> : null}
          </div>
        );
      })}
    </div>
  );
}
