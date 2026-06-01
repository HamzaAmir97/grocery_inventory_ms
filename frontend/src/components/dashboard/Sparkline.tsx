import { Line, LineChart } from "recharts";
import { ChartContainer } from "@/components/ui/chart";
import { SPARKLINE_CHART_CONFIG } from "./helpers";

export function Sparkline({ points }: { points: number[] }) {
  const data = points.map((count, index) => ({ index, count }));

  return (
    <ChartContainer
      config={SPARKLINE_CHART_CONFIG}
      className="dashboard-sparkline-chart"
      initialDimension={{ width: 80, height: 28 }}
    >
      <LineChart
        accessibilityLayer
        data={data}
        margin={{ top: 4, right: 2, bottom: 4, left: 2 }}
      >
        <Line
          dataKey="count"
          type="monotone"
          stroke="var(--color-count)"
          strokeWidth={2}
          dot={false}
          isAnimationActive={false}
        />
      </LineChart>
    </ChartContainer>
  );
}
