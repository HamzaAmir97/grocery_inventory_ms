import {
  Children,
  Fragment,
  isValidElement,
  useId,
  type ChangeEvent,
  type ReactNode,
  type SelectHTMLAttributes,
} from "react";
import {
  Select as ShadcnSelect,
  SelectContent as ShadcnSelectContent,
  SelectItem as ShadcnSelectItem,
  SelectTrigger as ShadcnSelectTrigger,
  SelectValue as ShadcnSelectValue,
} from "@/components/ui/select";
import { cn } from "@/lib/utils";
import { Field, type FieldProps } from "./Field";

type SelectOption = {
  disabled?: boolean;
  label: ReactNode;
  value: string;
};

const EMPTY_SELECT_VALUE = "__inventory_empty_select_value__";

function toSelectString(value: unknown) {
  if (Array.isArray(value)) return String(value[0] ?? "");
  return value === null || value === undefined ? "" : String(value);
}

function toRadixSelectValue(value: string) {
  return value === "" ? EMPTY_SELECT_VALUE : value;
}

function fromRadixSelectValue(value: string) {
  return value === EMPTY_SELECT_VALUE ? "" : value;
}

function collectSelectOptions(children: ReactNode): SelectOption[] {
  return Children.toArray(children).flatMap((child) => {
    if (!isValidElement<{ children?: ReactNode; disabled?: boolean; value?: string | number }>(child)) {
      return [];
    }

    if (child.type === Fragment) {
      return collectSelectOptions(child.props.children);
    }

    return [
      {
        disabled: child.props.disabled,
        label: child.props.children,
        value: toSelectString(child.props.value ?? child.props.children),
      },
    ];
  });
}

export function Select({ label, error, helper, optional, className = "", children, id, ...props }: SelectHTMLAttributes<HTMLSelectElement> & FieldProps) {
  const generatedId = useId();
  const selectId = id ?? generatedId;
  const { defaultValue, disabled, name, onChange, required, value } = props;
  const options = collectSelectOptions(children);
  const currentValue = toSelectString(value ?? defaultValue ?? "");
  const hasCurrentOption = options.some((option) => option.value === currentValue);
  const radixValue = hasCurrentOption ? toRadixSelectValue(currentValue) : undefined;

  function handleValueChange(nextValue: string) {
    const selectValue = fromRadixSelectValue(nextValue);

    onChange?.({
      currentTarget: { id: selectId, name, value: selectValue },
      target: { id: selectId, name, value: selectValue },
    } as ChangeEvent<HTMLSelectElement>);
  }

  return (
    <Field label={label} error={error} helper={helper} optional={optional} htmlFor={selectId}>
      <ShadcnSelect
        value={radixValue}
        onValueChange={handleValueChange}
        disabled={disabled}
        required={required}
      >
        <ShadcnSelectTrigger
          id={selectId}
          className={cn("select", error && "invalid", className)}
          aria-invalid={Boolean(error) || undefined}
        >
          <ShadcnSelectValue placeholder={options[0]?.label ?? "Select"} />
        </ShadcnSelectTrigger>
        <ShadcnSelectContent>
          {options.map((option) => (
            <ShadcnSelectItem
              key={`${option.value}-${String(option.label)}`}
              value={toRadixSelectValue(option.value)}
              disabled={option.disabled}
            >
              {option.label}
            </ShadcnSelectItem>
          ))}
        </ShadcnSelectContent>
      </ShadcnSelect>
    </Field>
  );
}
