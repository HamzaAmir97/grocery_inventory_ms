"use client";

import { useState } from "react";
import { IconRuler, StatusBadge } from "@/components/shared";
import { DEFAULT_PAGE_SIZE } from "@/constants/ui";
import {
  useDeleteUnitMutation,
  useUnitsQuery,
} from "@/hooks/settings";
import type { SettingsListFilters, Unit } from "@/types";
import { CountCell } from "./CountCell";
import { SettingsScreen } from "./SettingsScreen";
import { UnitEditor } from "./UnitEditor";

export function UnitsPageContent() {
  const [filters, setFilters] = useState<SettingsListFilters>({ page: 1, per_page: DEFAULT_PAGE_SIZE });
  const unitsQuery = useUnitsQuery(filters);
  const deleteUnitMutation = useDeleteUnitMutation();
  const error = unitsQuery.error instanceof Error ? unitsQuery.error.message : "";

  return (
    <SettingsScreen<Unit>
      title="Units of measure"
      description="Measurement units used by inventory items."
      addLabel="Add unit"
      icon={<IconRuler size={24} />}
      records={unitsQuery.data?.data ?? []}
      meta={unitsQuery.data?.meta}
      filters={filters}
      onFiltersChange={setFilters}
      isLoading={unitsQuery.isLoading}
      error={error}
      isDeleting={deleteUnitMutation.isPending}
      remove={(id) => deleteUnitMutation.mutateAsync(id)}
      columns={[
        { header: "Unit name", sortField: "name", render: (row) => <span style={{ fontWeight: 600 }}>{row.name}</span> },
        { header: "Symbol", sortField: "symbol", render: (row) => <span className="mono" style={{ background: "var(--color-surface-3)", padding: "2px 8px", borderRadius: 6 }}>{row.symbol || "—"}</span> },
        { header: "Status", sortField: "is_active", render: (row) => <StatusBadge active={row.is_active} /> },
        { header: "Items", sortField: "items_count", numeric: true, render: (row) => <CountCell value={row.items_count} /> },
      ]}
      renderEditor={({ editing, onClose, onSaved }) => (
        <UnitEditor editing={editing} onClose={onClose} onSaved={onSaved} />
      )}
    />
  );
}
