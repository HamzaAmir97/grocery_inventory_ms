import { IconAlertTriangle, IconBox, IconCategory, IconTruck } from "@/components/shared";
import type { ChartConfig } from "@/components/ui/chart";
import type { DashboardSummaryCard } from "@/types";

export const CATEGORY_CHART_COLORS = [
  "#ff7a00",
  "#ff9a2f",
  "#f6be1c",
  "#1f2940",
  "#9a7ff5",
  "#cbd5e1",
];

export const SPARKLINE_CHART_CONFIG = {
  count: {
    label: "Items",
    color: "#ff7a00",
  },
} satisfies ChartConfig;

export const INVENTORY_GROWTH_CHART_CONFIG = {
  count: {
    label: "Items added",
    color: "#ff7a00",
  },
} satisfies ChartConfig;

export const CATEGORY_BREAKDOWN_CHART_CONFIG = {
  items_count: {
    label: "Items",
  },
} satisfies ChartConfig;

export function statIcon(key: DashboardSummaryCard["key"]) {
  const size = 15;

  if (key === "categories") return <IconCategory size={size} />;
  if (key === "suppliers") return <IconTruck size={size} />;
  if (key === "low_stock") return <IconAlertTriangle size={size} />;
  return <IconBox size={size} />;
}
