import { queryOptions } from "@tanstack/react-query";
import { getDashboardStats } from "./dashboard.api";
import { dashboardKeys } from "./dashboard.keys";

export function dashboardStatsQueryOptions() {
  return queryOptions({
    queryKey: dashboardKeys.stats(),
    queryFn: getDashboardStats,
  });
}
