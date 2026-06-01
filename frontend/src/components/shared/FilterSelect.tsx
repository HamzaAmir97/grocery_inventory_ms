import type { SelectHTMLAttributes } from "react";
import { Select } from "./Select";

export function FilterSelect(props: SelectHTMLAttributes<HTMLSelectElement>) {
  return <Select {...props} />;
}
