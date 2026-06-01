import { Skeleton } from "@/components/ui/skeleton";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";

export function RecentItemsTableSkeleton({ rows }: { rows: number }) {
  return (
    <div className="dashboard-recent-table" aria-hidden="true">
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
          {Array.from({ length: rows }).map((_, index) => (
            <TableRow key={index}>
              <TableCell>
                <div className="dashboard-item-cell">
                  <Skeleton className="skeleton-line skeleton-w-28" />
                  <Skeleton className="skeleton-line skeleton-w-20" />
                </div>
              </TableCell>
              <TableCell><Skeleton className="skeleton-line skeleton-w-24" /></TableCell>
              <TableCell><Skeleton className="skeleton-line skeleton-w-16" /></TableCell>
              <TableCell><Skeleton className="skeleton-pill skeleton-w-20" /></TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </div>
  );
}
