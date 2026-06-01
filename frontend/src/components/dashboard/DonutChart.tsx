import { Cell, Pie, PieChart } from "recharts";
import { ChartContainer, ChartTooltip, ChartTooltipContent } from "@/components/ui/chart";
import { formatNumber } from "@/lib/format";
import type { DashboardStats as DashboardStatsType } from "@/types";
import { CATEGORY_BREAKDOWN_CHART_CONFIG, CATEGORY_CHART_COLORS } from "./helpers";

export function DonutChart({
  items,
  total,
}: {
  items: DashboardStatsType["category_breakdown"]["items"];
  total: number;
}) {
  const chartData = items.slice(0, 5).map((item, index) => ({
    ...item,
    fill: CATEGORY_CHART_COLORS[index % CATEGORY_CHART_COLORS.length],
  }));

  return (
    <div className="dashboard-donut-chart-shell">
      <ChartContainer
        config={CATEGORY_BREAKDOWN_CHART_CONFIG}
        className="dashboard-donut-chart"
        initialDimension={{ width: 148, height: 148 }}
      >
        <PieChart
          accessibilityLayer
          margin={{ top: 0, right: 0, bottom: 0, left: 0 }}
        >
          <ChartTooltip
            cursor={false}
            content={<ChartTooltipContent hideLabel nameKey="name" />}
          />
          <Pie
            data={chartData}
            dataKey="items_count"
            nameKey="name"
            innerRadius={42}
            outerRadius={66}
            paddingAngle={2}
            strokeWidth={2}
          >
            {chartData.map((item) => (
              <Cell key={item.name} fill={item.fill} />
            ))}
          </Pie>
        </PieChart>
      </ChartContainer>
      <div className="dashboard-donut-hole">
        <span>Total</span>
        <strong>{formatNumber(total)}</strong>
      </div>
    </div>
  );
}
