"use client";

import { ErrorAlert } from "@/components/shared";
import { useDashboardQuery } from "@/hooks/dashboard";
import { CategoryBreakdownCard } from "./CategoryBreakdownCard";
import { DashboardSkeleton } from "./DashboardSkeleton";
import { DashboardStatCards } from "./DashboardStatCards";
import { InventoryGrowthCard } from "./InventoryGrowthCard";
import { LowStockPanel } from "./LowStockPanel";
import { RecentItemsTable } from "./RecentItemsTable";

export function DashboardPageContent() {
  const dashboardQuery = useDashboardQuery();
  const stats = dashboardQuery.data?.data ?? null;
  const error =
    dashboardQuery.error instanceof Error ? dashboardQuery.error.message : "";

  return (
    <div className="dashboard-page">
      {dashboardQuery.isLoading ? <DashboardSkeleton /> : null}
      {error ? <ErrorAlert message={error} /> : null}

      {!dashboardQuery.isLoading && !error && stats ? (
        <>
          <DashboardStatCards
            cards={stats.summary_cards}
            growth={stats.inventory_growth}
          />
          <div className="dashboard-chart-grid">
            <InventoryGrowthCard
              points={stats.inventory_growth}
              year={stats.inventory_growth_year}
            />
            <CategoryBreakdownCard breakdown={stats.category_breakdown} />
          </div>
          <div className="dashboard-bottom-grid">
            <LowStockPanel items={stats.low_stock_list} />
            <RecentItemsTable items={stats.recent_items} />
          </div>
        </>
      ) : null}
    </div>
  );
}
