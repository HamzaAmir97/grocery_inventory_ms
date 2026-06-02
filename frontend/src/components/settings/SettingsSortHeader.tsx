import { IconChevronDown } from "@/components/shared";
import { Button as ShadcnButton } from "@/components/ui/button";
import type { SettingsListFilters } from "@/types";
import type { SettingsSortField } from "./helpers";

export function SettingsSortHeader({
  align = "start",
  field,
  filters,
  label,
  onSort,
}: {
  align?: "start" | "end";
  field: SettingsSortField;
  filters: SettingsListFilters;
  label: string;
  onSort: (field: SettingsSortField) => void;
}) {
  const active = filters.sort_by === field;
  const direction = active ? (filters.sort_dir ?? "asc") : undefined;

  return (
    <ShadcnButton
      type="button"
      variant="ghost"
      size="sm"
      className={`table-sort-button ${align === "end" ? "table-sort-button-end" : ""}`}
      aria-label={`Sort by ${label}`}
      aria-sort={active ? (direction === "asc" ? "ascending" : "descending") : undefined}
      onClick={() => onSort(field)}
    >
      <span>{label}</span>
      <IconChevronDown
        size={13}
        className={`table-sort-icon ${active ? "active" : ""} ${direction === "asc" ? "asc" : ""}`}
      />
    </ShadcnButton>
  );
}
