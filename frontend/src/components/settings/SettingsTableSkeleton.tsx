import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Skeleton } from "@/components/ui/skeleton";
import type { Column } from "./helpers";

export function SettingsTableSkeleton<T>({
  columns,
  rows,
}: {
  columns: Column<T>[];
  rows: number;
}) {
  return (
    <div className="table-wrap" aria-hidden="true">
      <div className="table-scroll">
        <Table className="tbl">
          <TableHeader>
            <TableRow>
              {columns.map((column) => (
                <TableHead key={column.header} className={column.numeric ? "num" : undefined}>
                  {column.header}
                </TableHead>
              ))}
              <TableHead style={{ textAlign: "right" }}>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {Array.from({ length: rows }).map((_, rowIndex) => (
              <TableRow key={rowIndex}>
                {columns.map((column, columnIndex) => (
                  <TableCell key={column.header} className={column.numeric ? "num" : undefined}>
                    {columnIndex === 0 ? (
                      <div className="table-skeleton-name-cell">
                        <Skeleton className="skeleton-circle skeleton-size-7" />
                        <Skeleton className="skeleton-line skeleton-w-28" />
                      </div>
                    ) : column.numeric ? (
                      <Skeleton className="skeleton-pill skeleton-w-12 skeleton-ml-auto" />
                    ) : column.header === "Status" ? (
                      <Skeleton className="skeleton-pill skeleton-w-20" />
                    ) : (
                      <Skeleton className="skeleton-line skeleton-w-32" />
                    )}
                  </TableCell>
                ))}
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
