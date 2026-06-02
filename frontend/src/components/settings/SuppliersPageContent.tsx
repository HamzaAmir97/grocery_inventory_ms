"use client";

import { useState } from "react";
import { IconTruck, StatusBadge } from "@/components/shared";
import { DEFAULT_PAGE_SIZE } from "@/constants/ui";
import {
  useDeleteSupplierMutation,
  useSuppliersQuery,
} from "@/hooks/settings";
import type { SettingsListFilters, Supplier } from "@/types";
import { CountCell } from "./CountCell";
import { NameCell } from "./NameCell";
import { SettingsScreen } from "./SettingsScreen";
import { SupplierEditor } from "./SupplierEditor";

export function SuppliersPageContent() {
  const [filters, setFilters] = useState<SettingsListFilters>({ page: 1, per_page: DEFAULT_PAGE_SIZE });
  const suppliersQuery = useSuppliersQuery(filters);
  const deleteSupplierMutation = useDeleteSupplierMutation();
  const error = suppliersQuery.error instanceof Error ? suppliersQuery.error.message : "";

  return (
    <SettingsScreen<Supplier>
      title="Suppliers"
      description="Vendors that supply your inventory items."
      addLabel="Add supplier"
      icon={<IconTruck size={24} />}
      records={suppliersQuery.data?.data ?? []}
      meta={suppliersQuery.data?.meta}
      filters={filters}
      onFiltersChange={setFilters}
      isLoading={suppliersQuery.isLoading}
      error={error}
      isDeleting={deleteSupplierMutation.isPending}
      remove={(id) => deleteSupplierMutation.mutateAsync(id)}
      columns={[
        { header: "Supplier", sortField: "name", render: (row) => <NameCell name={row.name} /> },
        { header: "Contact", sortField: "contact_person", render: (row) => <span>{row.contact_person || "—"}</span> },
        { header: "Phone", sortField: "phone", render: (row) => <span className="muted">{row.phone || "—"}</span> },
        { header: "Email", sortField: "email", render: (row) => <span className="muted">{row.email || "—"}</span> },
        { header: "Status", sortField: "is_active", render: (row) => <StatusBadge active={row.is_active} /> },
        { header: "Items", sortField: "items_count", numeric: true, render: (row) => <CountCell value={row.items_count} /> },
      ]}
      renderEditor={({ editing, onClose, onSaved }) => (
        <SupplierEditor editing={editing} onClose={onClose} onSaved={onSaved} />
      )}
    />
  );
}
