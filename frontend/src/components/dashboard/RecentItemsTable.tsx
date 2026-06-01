import Link from "next/link";
import { ROUTES } from "@/constants";
import {
  Card,
  EmptyState,
  IconArrowRight,
  IconBox,
  Pill,
} from "@/components/shared";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { formatCurrency } from "@/lib/format";
import type { DashboardItem } from "@/types";

export function RecentItemsTable({ items }: { items: DashboardItem[] }) {
  return (
    <Card className="dashboard-panel dashboard-recent-panel">
      <div className="dashboard-panel-head">
        <div>
          <h2>Recent items</h2>
        </div>
        <Link
          href={ROUTES.inventory}
          className="dashboard-manage-link dashboard-manage-link-inline"
        >
          <span>View all</span>
          <IconArrowRight size={13} />
        </Link>
      </div>

      {items.length === 0 ? (
        <EmptyState
          icon={<IconBox size={24} />}
          title="No items yet"
          message="Add your first inventory item to populate this view."
        />
      ) : (
        <div className="dashboard-recent-table">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Item</TableHead>
                <TableHead>Category</TableHead>
                <TableHead>Price</TableHead>
                <TableHead>Status</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {items.map((item) => (
                <TableRow key={item.id}>
                  <TableCell>
                    <div className="dashboard-item-cell">
                      <div className="dashboard-item-name">{item.name}</div>
                      <div className="dashboard-item-meta">{item.supplier}</div>
                    </div>
                  </TableCell>
                  <TableCell>{item.category}</TableCell>
                  <TableCell>{formatCurrency(Number(item.price))}</TableCell>
                  <TableCell>
                    <Pill tone={item.status_tone} dot>
                      {item.status_label}
                    </Pill>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </div>
      )}
    </Card>
  );
}
