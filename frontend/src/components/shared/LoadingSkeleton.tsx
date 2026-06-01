import { Skeleton as ShadcnSkeleton } from "@/components/ui/skeleton";

export function LoadingSkeleton({ rows = 1, height = 56 }: { rows?: number; height?: number }) {
  return (
    <div className="grid" style={{ display: "grid", gap: 10 }}>
      {Array.from({ length: rows }).map((_, index) => (
        <ShadcnSkeleton key={index} className="skeleton" style={{ height }} />
      ))}
    </div>
  );
}
