import type { ReactNode } from "react";
import type { PaginatedResponse, SettingBase, SettingsListFilters } from "@/types";

export type SettingsSortField = NonNullable<SettingsListFilters["sort_by"]>;

export type Column<T> = {
  header: string;
  render: (row: T) => ReactNode;
  numeric?: boolean;
  sortField?: SettingsSortField;
};

export type SettingsScreenProps<T extends SettingBase> = {
  title: string;
  description: string;
  addLabel: string;
  icon: ReactNode;
  records: T[];
  meta?: PaginatedResponse<T>["meta"];
  filters: SettingsListFilters;
  onFiltersChange: (filters: SettingsListFilters) => void;
  isLoading: boolean;
  error?: string;
  isDeleting?: boolean;
  remove: (id: number) => Promise<unknown>;
  columns: Column<T>[];
  renderEditor: (args: { editing: T | null; onClose: () => void; onSaved: () => void }) => ReactNode;
  parentFilter?: ReactNode;
  matchesParent?: (row: T) => boolean;
};
