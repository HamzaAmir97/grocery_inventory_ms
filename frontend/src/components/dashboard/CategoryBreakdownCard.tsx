import Link from "next/link";
import { ROUTES } from "@/constants";
import { Card, EmptyState } from "@/components/shared";
import type { DashboardStats as DashboardStatsType } from "@/types";
import { CATEGORY_CHART_COLORS } from "./helpers";
import { DonutChart } from "./DonutChart";

export function CategoryBreakdownCard({
  breakdown,
}: {
  breakdown: DashboardStatsType["category_breakdown"];
}) {
  return (
    <Card className="dashboard-panel dashboard-donut-panel">
      <div className="dashboard-panel-head">
        <div>
          <h2>By category</h2>
        </div>
        <Link
          href={ROUTES.settingsCategories}
          className="dashboard-manage-link"
        >
          Manage
        </Link>
      </div>

      {breakdown.items.length === 0 ? (
        <EmptyState
          title="No category data"
          message="Add items to see the category distribution."
        />
      ) : (
        <div className="dashboard-donut-wrap">
          <DonutChart items={breakdown.items} total={breakdown.total} />
          <div className="dashboard-donut-legend">
            {breakdown.items.slice(0, 5).map((item, index) => (
              <div key={item.name} className="dashboard-legend-row">
                <div className="dashboard-legend-label">
                  <span
                    className="dashboard-legend-dot"
                    style={{
                      background:
                        CATEGORY_CHART_COLORS[
                          index % CATEGORY_CHART_COLORS.length
                        ],
                    }}
                  />
                  <span>{item.name}</span>
                </div>
                <span>{item.percentage}%</span>
              </div>
            ))}
          </div>
        </div>
      )}
    </Card>
  );
}
