import { Skeleton } from "@/components/ui/skeleton";

export function ItemFormSkeleton() {
  return (
    <div className="form-grid" aria-hidden="true">
      <div className="span-2 skeleton-stack">
        <Skeleton className="skeleton-line skeleton-w-20" />
        <Skeleton className="skeleton-field" />
      </div>
      <div className="skeleton-stack">
        <Skeleton className="skeleton-line skeleton-w-12" />
        <Skeleton className="skeleton-field" />
      </div>
      <div className="skeleton-stack">
        <Skeleton className="skeleton-line skeleton-w-16" />
        <Skeleton className="skeleton-field" />
      </div>
      <div className="span-2 skeleton-stack">
        <Skeleton className="skeleton-line skeleton-w-24" />
        <Skeleton className="skeleton-textarea" />
      </div>
    </div>
  );
}
