import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import type { ApiSuccessResponse, DashboardStats } from "@/types";

export async function getDashboardStats() {
  const response = await axiosInstance.get<ApiSuccessResponse<DashboardStats>>(API_PATHS.DASHBOARD.STATS);

  return response.data;
}
