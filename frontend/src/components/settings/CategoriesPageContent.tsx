"use client";

import { useState } from "react";
import { IconCategory, StatusBadge } from "@/components/shared";
import { DEFAULT_PAGE_SIZE } from "@/constants/ui";
import {
  useCategoriesQuery,
  useDeleteCategoryMutation,
} from "@/hooks/settings";
import type { Category, SettingsListFilters } from "@/types";
import { CategoryEditor } from "./CategoryEditor";
import { CountCell } from "./CountCell";
import { NameCell } from "./NameCell";
import { SettingsScreen } from "./SettingsScreen";

export function CategoriesPageContent() {
  const [filters, setFilters] = useState<SettingsListFilters>({ page: 1, per_page: DEFAULT_PAGE_SIZE });
  const categoriesQuery = useCategoriesQuery(filters);
  const deleteCategoryMutation = useDeleteCategoryMutation();
  const error = categoriesQuery.error instanceof Error ? categoriesQuery.error.message : "";

  return (
    <SettingsScreen<Category>
      title="Categories"
      description="Top-level grouping for inventory items."
      addLabel="Add category"
      icon={<IconCategory size={24} />}
      records={categoriesQuery.data?.data ?? []}
      meta={categoriesQuery.data?.meta}
      filters={filters}
      onFiltersChange={setFilters}
      isLoading={categoriesQuery.isLoading}
      error={error}
      isDeleting={deleteCategoryMutation.isPending}
      remove={(id) => deleteCategoryMutation.mutateAsync(id)}
      columns={[
        { header: "Name", sortField: "name", render: (row) => <NameCell name={row.name} /> },
        { header: "Description", sortField: "description", render: (row) => <span className="muted">{row.description || "—"}</span> },
        { header: "Status", sortField: "is_active", render: (row) => <StatusBadge active={row.is_active} /> },
        { header: "Subcategories", sortField: "subcategories_count", numeric: true, render: (row) => <CountCell value={row.subcategories_count} /> },
        { header: "Items", sortField: "items_count", numeric: true, render: (row) => <CountCell value={row.items_count} /> },
      ]}
      renderEditor={({ editing, onClose, onSaved }) => (
        <CategoryEditor editing={editing} onClose={onClose} onSaved={onSaved} />
      )}
    />
  );
}
