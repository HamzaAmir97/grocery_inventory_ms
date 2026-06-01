import { Card, IconChevronDown } from "@/components/shared";
import { Button as ShadcnButton } from "@/components/ui/button";
import type { DashboardGrowthPoint } from "@/types";
import { AreaChart } from "./AreaChart";

export function InventoryGrowthCard({
  points,
  year,
}: {
  points: DashboardGrowthPoint[];
  year: number;
}) {
  return (
    <Card className="dashboard-panel dashboard-growth-panel">
      <div className="dashboard-panel-head">
        <div>
          <h2>Inventory growth</h2>
          <p>Items added per month</p>
        </div>
        <ShadcnButton
          type="button"
          variant="outline"
          size="sm"
          className="dashboard-year-pill"
        >
          <span>{year}</span>
          <IconChevronDown size={12} />
        </ShadcnButton>
      </div>
      <AreaChart points={points} />
    </Card>
  );
}
