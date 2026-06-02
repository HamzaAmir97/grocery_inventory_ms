import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Skeleton } from "@/components/ui/skeleton";

export function InventoryTableSkeleton({ rows }: { rows: number }) {
  return (
    <div className="table-wrap" aria-hidden="true">
      <div className="table-scroll">
        <Table className="tbl">
          <TableHeader>
            <TableRow>
              <TableHead>Item name</TableHead>
              <TableHead>SKU</TableHead>
              <TableHead>Category</TableHead>
              <TableHead>Subcategory</TableHead>
              <TableHead>Unit</TableHead>
              <TableHead>Supplier</TableHead>
              <TableHead className="num">Price</TableHead>
              <TableHead className="num">Stock</TableHead>
              <TableHead>Stock status</TableHead>
              <TableHead style={{ textAlign: "right" }}>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {Array.from({ length: rows }).map((_, index) => (
              <TableRow key={index} className={index % 3 === 1 ? "low-stock" : ""}>
                <TableCell>
                  <div className="table-skeleton-name-cell">
                    <Skeleton className="skeleton-circle skeleton-size-8" />
                    <div className="skeleton-stack">
                      <Skeleton className="skeleton-line skeleton-w-32" />
                      <Skeleton className="skeleton-line skeleton-w-20" />
                    </div>
                  </div>
                </TableCell>
                <TableCell><Skeleton className="skeleton-pill skeleton-w-20" /></TableCell>
                <TableCell><Skeleton className="skeleton-line skeleton-w-24" /></TableCell>
                <TableCell><Skeleton className="skeleton-line skeleton-w-24" /></TableCell>
                <TableCell><Skeleton className="skeleton-line skeleton-w-12" /></TableCell>
                <TableCell><Skeleton className="skeleton-line skeleton-w-36" /></TableCell>
                <TableCell className="num"><Skeleton className="skeleton-line skeleton-w-16 skeleton-ml-auto" /></TableCell>
                <TableCell className="num"><Skeleton className="skeleton-line skeleton-w-14 skeleton-ml-auto" /></TableCell>
                <TableCell><Skeleton className="skeleton-pill skeleton-w-24" /></TableCell>
                <TableCell>
                  <div className="table-action-skeleton">
                    <Skeleton className="skeleton-square skeleton-size-7" />
                    <Skeleton className="skeleton-square skeleton-size-7" />
                  </div>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>
    </div>
  );
}
