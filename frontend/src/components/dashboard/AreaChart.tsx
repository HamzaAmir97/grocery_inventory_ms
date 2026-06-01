import { CartesianGrid, Line, LineChart, XAxis, YAxis } from "recharts";
import { ChartContainer, ChartTooltip, ChartTooltipContent } from "@/components/ui/chart";
import { formatNumber } from "@/lib/format";
import type { DashboardGrowthPoint } from "@/types";
import { INVENTORY_GROWTH_CHART_CONFIG } from "./helpers";

export function AreaChart({ points }: { points: DashboardGrowthPoint[] }) {
  const data = points.map((point) => ({
    month: point.month,
    count: point.count,
  }));

  return (
    <ChartContainer
      config={INVENTORY_GROWTH_CHART_CONFIG}
      className="dashboard-area-chart"
      initialDimension={{ width: 720, height: 320 }}
    >
      <LineChart
        accessibilityLayer
        data={data}
        margin={{ top: 10, right: 18, bottom: 4, left: 0 }}
      >
        <CartesianGrid vertical={false} strokeDasharray="4 5" />
        <XAxis
          dataKey="month"
          tickLine={false}
          axisLine={false}
          tickMargin={12}
        />
        <YAxis
          tickLine={false}
          axisLine={false}
          width={34}
          tickFormatter={(value) => formatNumber(Number(value))}
        />
        <ChartTooltip
          cursor={false}
          content={<ChartTooltipContent indicator="line" />}
        />
        <Line
          dataKey="count"
          type="monotone"
          stroke="var(--color-count)"
          strokeWidth={3}
          dot={{ fill: "var(--color-count)", r: 3 }}
          activeDot={{ r: 6 }}
        />
      </LineChart>
    </ChartContainer>
  );
}
