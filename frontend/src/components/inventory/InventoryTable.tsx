import Link from "next/link";
import { ROUTES } from "@/constants";
import {
  Avatar,
  IconEdit,
  IconTrash,
  LowStockBadge,
} from "@/components/shared";
import { Button as ShadcnButton } from "@/components/ui/button";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { formatCurrency, formatNumber, initials } from "@/lib/format";
import type { Item, ItemFilters } from "@/types";
import { InventorySortHeader } from "./InventorySortHeader";
import type { InventorySortField } from "./helpers";

export function InventoryTable({
  filters,
  items,
  onDelete,
  onSort,
}: {
  filters: ItemFilters;
  items: Item[];
  onDelete: (item: Item) => void;
  onSort: (patch: Partial<ItemFilters>) => void;
}) {
  function sortBy(field: InventorySortField) {
    const nextDirection =
      filters.sort_by === field && filters.sort_dir === "asc" ? "desc" : "asc";

    onSort({ sort_by: field, sort_dir: nextDirection });
  }

  return (
    <div className="table-wrap">
      <div className="table-scroll">
        <Table className="tbl">
          <TableHeader>
            <TableRow>
              <TableHead>
                <InventorySortHeader
                  field="name"
                  filters={filters}
                  label="Item name"
                  onSort={sortBy}
                />
              </TableHead>
              <TableHead>
                <InventorySortHeader
                  field="sku"
                  filters={filters}
                  label="SKU"
                  onSort={sortBy}
                />
              </TableHead>
              <TableHead>
                <InventorySortHeader
                  field="category"
                  filters={filters}
                  label="Category"
                  onSort={sortBy}
                />
              </TableHead>
              <TableHead>
                <InventorySortHeader
                  field="subcategory"
                  filters={filters}
                  label="Subcategory"
                  onSort={sortBy}
                />
              </TableHead>
              <TableHead>
                <InventorySortHeader
                  field="unit"
                  filters={filters}
                  label="Unit"
                  onSort={sortBy}
                />
              </TableHead>
              <TableHead>
                <InventorySortHeader
                  field="supplier"
                  filters={filters}
                  label="Supplier"
                  onSort={sortBy}
                />
              </TableHead>
              <TableHead className="num">
                <InventorySortHeader
                  align="end"
                  field="price"
                  filters={filters}
                  label="Price"
                  onSort={sortBy}
                />
              </TableHead>
              <TableHead className="num">
                <InventorySortHeader
                  align="end"
                  field="stock_quantity"
                  filters={filters}
                  label="Stock"
                  onSort={sortBy}
                />
              </TableHead>
              <TableHead>Stock status</TableHead>
              <TableHead style={{ textAlign: "right" }}>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {items.map((item) => {
              const low = item.stock_quantity <= item.low_stock_threshold;
              return (
                <TableRow key={item.id} className={low ? "low-stock" : ""}>
                  <TableCell>
                    <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
                      <Avatar initials={initials(item.name)} size={30} />
                      <span style={{ fontWeight: 600 }}>{item.name}</span>
                    </div>
                  </TableCell>
                  <TableCell className="mono muted">{item.sku}</TableCell>
                  <TableCell>{item.category?.name ?? "—"}</TableCell>
                  <TableCell>{item.subcategory?.name ?? "—"}</TableCell>
                  <TableCell>{item.unit?.symbol ?? item.unit?.name ?? "—"}</TableCell>
                  <TableCell>{item.supplier?.name ?? "—"}</TableCell>
                  <TableCell className="num">{formatCurrency(item.price)}</TableCell>
                  <TableCell className="num">{formatNumber(item.stock_quantity)}</TableCell>
                  <TableCell><LowStockBadge isLowStock={low} /></TableCell>
                  <TableCell>
                    <div style={{ display: "flex", gap: 6, justifyContent: "flex-end" }}>
                      <ShadcnButton asChild variant="ghost" size="icon-sm" className="btn-icon" aria-label={`Edit ${item.name}`}>
                        <Link href={ROUTES.inventoryEdit(item.id)}>
                          <IconEdit size={16} />
                        </Link>
                      </ShadcnButton>
                      <ShadcnButton type="button" variant="ghost" size="icon-sm" className="btn-icon" aria-label={`Delete ${item.name}`} onClick={() => onDelete(item)} style={{ color: "var(--color-danger)" }}>
                        <IconTrash size={16} />
                      </ShadcnButton>
                    </div>
                  </TableCell>
                </TableRow>
              );
            })}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
