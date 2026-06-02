import { ITEM_SORT_OPTIONS } from "@/constants/table-columns";
import { Card, Input, Select } from "@/components/shared";
import type { ItemFilters, LookupOption } from "@/types";

export function InventoryToolbar({
  filters,
  categories,
  suppliers,
  onChange,
}: {
  filters: ItemFilters;
  categories: LookupOption[];
  suppliers: LookupOption[];
  onChange: (patch: Partial<ItemFilters>) => void;
}) {
  return (
    <Card className="inventory-toolbar" style={{ display: "grid", gap: 12, gridTemplateColumns: "repeat(auto-fit, minmax(160px, 1fr))", alignItems: "end" }}>
      <Input
        label="Search"
        placeholder="Search by name or SKU"
        value={filters.search ?? ""}
        onChange={(event) => onChange({ search: event.target.value })}
      />
      <Select label="Category" value={filters.category_id ?? ""} onChange={(event) => onChange({ category_id: event.target.value ? Number(event.target.value) : undefined })}>
        <option value="">All categories</option>
        {categories.map((option) => (
          <option key={option.id} value={option.id}>{option.name}</option>
        ))}
      </Select>
      <Select label="Supplier" value={filters.supplier_id ?? ""} onChange={(event) => onChange({ supplier_id: event.target.value ? Number(event.target.value) : undefined })}>
        <option value="">All suppliers</option>
        {suppliers.map((option) => (
          <option key={option.id} value={option.id}>{option.name}</option>
        ))}
      </Select>
      <Select label="Stock level" value={filters.low_stock ? "true" : ""} onChange={(event) => onChange({ low_stock: event.target.value === "true" ? true : undefined })}>
        <option value="">All stock levels</option>
        <option value="true">Low stock only</option>
      </Select>
      <Select
        label="Sort by"
        value={filters.sort_by ?? "created_at"}
        onChange={(event) => {
          const sortBy = event.target.value as ItemFilters["sort_by"];
          onChange({ sort_by: sortBy, sort_dir: sortBy === "created_at" ? "desc" : "asc" });
        }}
      >
        {ITEM_SORT_OPTIONS.map((option) => (
          <option key={option.value} value={option.value}>{option.label}</option>
        ))}
      </Select>
    </Card>
  );
}
