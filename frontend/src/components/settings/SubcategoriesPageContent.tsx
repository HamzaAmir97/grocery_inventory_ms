"use client";

import { useState } from "react";
import { IconCategory, Pill, Select, StatusBadge } from "@/components/shared";
import { DEFAULT_PAGE_SIZE } from "@/constants/ui";
import { useCategoriesLookupQuery } from "@/hooks/lookups";
import {
  useDeleteSubcategoryMutation,
  useSubcategoriesQuery,
} from "@/hooks/settings";
import type { SettingsListFilters, Subcategory } from "@/types";
import { CountCell } from "./CountCell";
import { NameCell } from "./NameCell";
import { SettingsScreen } from "./SettingsScreen";
import { SubcategoryEditor } from "./SubcategoryEditor";

export function SubcategoriesPageContent() {
  const [filters, setFilters] = useState<SettingsListFilters>({ page: 1, per_page: DEFAULT_PAGE_SIZE });
  const categoriesQuery = useCategoriesLookupQuery();
  const subcategoriesQuery = useSubcategoriesQuery(filters);
  const deleteSubcategoryMutation = useDeleteSubcategoryMutation();
  const [parent, setParent] = useState<number | "">("");
  const categories = categoriesQuery.data?.data ?? [];
  const error = subcategoriesQuery.error instanceof Error ? subcategoriesQuery.error.message : "";

  return (
    <SettingsScreen<Subcategory>
      title="Subcategories"
      description="Nested grouping under a parent category."
      addLabel="Add subcategory"
      icon={<IconCategory size={24} />}
      records={subcategoriesQuery.data?.data ?? []}
      meta={subcategoriesQuery.data?.meta}
      filters={filters}
      onFiltersChange={setFilters}
      isLoading={subcategoriesQuery.isLoading}
      error={error}
      isDeleting={deleteSubcategoryMutation.isPending}
      remove={(id) => deleteSubcategoryMutation.mutateAsync(id)}
      matchesParent={parent === "" ? undefined : (row) => row.category_id === parent}
      parentFilter={
        <div style={{ width: 200 }}>
          <Select label="Parent category" value={parent} onChange={(event) => setParent(event.target.value ? Number(event.target.value) : "")}>
            <option value="">All categories</option>
            {categories.map((option) => <option key={option.id} value={option.id}>{option.name}</option>)}
          </Select>
        </div>
      }
      columns={[
        { header: "Name", sortField: "name", render: (row) => <NameCell name={row.name} /> },
        { header: "Parent category", sortField: "category", render: (row) => <Pill tone="accent" dot={false}>{row.category?.name ?? categories.find((c) => c.id === row.category_id)?.name ?? "—"}</Pill> },
        { header: "Description", sortField: "description", render: (row) => <span className="muted">{row.description || "—"}</span> },
        { header: "Status", sortField: "is_active", render: (row) => <StatusBadge active={row.is_active} /> },
        { header: "Items", sortField: "items_count", numeric: true, render: (row) => <CountCell value={row.items_count} /> },
      ]}
      renderEditor={({ editing, onClose, onSaved }) => (
        <SubcategoryEditor editing={editing} categories={categories} onClose={onClose} onSaved={onSaved} />
      )}
    />
  );
}
