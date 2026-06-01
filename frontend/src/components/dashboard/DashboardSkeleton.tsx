import { Card } from "@/components/shared";
import { Skeleton } from "@/components/ui/skeleton";
import { RecentItemsTableSkeleton } from "./RecentItemsTableSkeleton";

export function DashboardSkeleton() {
  return (
    <>
      <div className="dashboard-stat-grid" aria-hidden="true">
        {Array.from({ length: 4 }).map((_, index) => (
          <div key={index} className="dashboard-stat-card">
            <div className="dashboard-stat-top">
              <div className="dashboard-stat-label-wrap">
                <Skeleton className="skeleton-circle skeleton-size-8" />
                <Skeleton className="skeleton-line skeleton-w-24" />
              </div>
              <Skeleton className="skeleton-pill" />
            </div>
            <div className="dashboard-stat-bottom">
              <Skeleton className="skeleton-line skeleton-w-18 skeleton-h-7" />
              <div className="dashboard-sparkline-skeleton">
                <Skeleton className="skeleton-line skeleton-w-full" />
              </div>
            </div>
          </div>
        ))}
      </div>

      <div className="dashboard-chart-grid" aria-hidden="true">
        <Card className="dashboard-panel dashboard-growth-panel">
          <div className="dashboard-panel-head">
            <div className="skeleton-stack">
              <Skeleton className="skeleton-line skeleton-w-36" />
              <Skeleton className="skeleton-line skeleton-w-28" />
            </div>
            <Skeleton className="skeleton-pill skeleton-w-16" />
          </div>
          <div className="dashboard-chart-skeleton">
            {Array.from({ length: 6 }).map((_, index) => (
              <Skeleton
                key={index}
                className="dashboard-chart-skeleton-dot"
                style={{ left: `${8 + index * 17}%`, top: `${58 - (index % 3) * 12}%` }}
              />
            ))}
          </div>
        </Card>

        <Card className="dashboard-panel dashboard-donut-panel">
          <div className="dashboard-panel-head">
            <Skeleton className="skeleton-line skeleton-w-28" />
            <Skeleton className="skeleton-line skeleton-w-14" />
          </div>
          <div className="dashboard-donut-wrap">
            <Skeleton className="dashboard-donut-skeleton" />
            <div className="dashboard-donut-legend">
              {Array.from({ length: 5 }).map((_, index) => (
                <div key={index} className="dashboard-legend-row">
                  <div className="dashboard-legend-label">
                    <Skeleton className="skeleton-circle skeleton-size-2" />
                    <Skeleton className="skeleton-line skeleton-w-24" />
                  </div>
                  <Skeleton className="skeleton-line skeleton-w-10" />
                </div>
              ))}
            </div>
          </div>
        </Card>
      </div>

      <div className="dashboard-bottom-grid" aria-hidden="true">
        <Card className="dashboard-panel dashboard-low-stock-panel">
          <div className="dashboard-panel-head">
            <Skeleton className="skeleton-line skeleton-w-32" />
            <Skeleton className="skeleton-pill skeleton-w-16" />
          </div>
          <div className="dashboard-low-stock-list">
            {Array.from({ length: 4 }).map((_, index) => (
              <div key={index} className="dashboard-low-stock-item">
                <Skeleton className="skeleton-circle skeleton-size-4" />
                <div className="dashboard-low-stock-copy">
                  <Skeleton className="skeleton-line skeleton-w-32" />
                  <Skeleton className="skeleton-line skeleton-w-20" />
                </div>
                <div className="dashboard-low-stock-qty">
                  <Skeleton className="skeleton-line skeleton-w-12" />
                  <Skeleton className="skeleton-line skeleton-w-16" />
                </div>
              </div>
            ))}
          </div>
        </Card>

        <Card className="dashboard-panel dashboard-recent-panel">
          <div className="dashboard-panel-head">
            <Skeleton className="skeleton-line skeleton-w-24" />
            <Skeleton className="skeleton-line skeleton-w-16" />
          </div>
          <RecentItemsTableSkeleton rows={5} />
        </Card>
      </div>
    </>
  );
}
