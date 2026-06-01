"use client";

import { useQuery } from "@tanstack/react-query";
import { dashboardStatsQueryOptions } from "@/lib/dashboard/actions";

export function useDashboardQuery() {
  return useQuery(dashboardStatsQueryOptions());
}
