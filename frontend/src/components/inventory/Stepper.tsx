import type { ItemFormValues } from "@/types";
import { IconCheck } from "@/components/shared";
import { STEP_META } from "./helpers";

export function Stepper({ step, errors }: { step: number; errors: Partial<Record<keyof ItemFormValues, string>> }) {
  const hasErrors = Object.keys(errors).length > 0;
  return (
    <nav className="wizard-stepper" aria-label="Inventory item progress">
      {STEP_META.map((item, index) => {
        const state = index < step ? "done" : index === step ? (hasErrors ? "error" : "active") : "todo";
        return (
          <div key={item.label} className={`wizard-step ${state} ${index < step ? "connector-done" : ""}`} aria-current={index === step ? "step" : undefined}>
            <span className={`wizard-step-circle ${state}`}>
              {state === "done" ? <IconCheck size={14} stroke={2.5} /> : index + 1}
            </span>
            <span className={`wizard-step-label ${state}`}>{item.shortLabel}</span>
            <span className="wizard-step-subtitle">{item.subtitle}</span>
          </div>
        );
      })}
    </nav>
  );
}
