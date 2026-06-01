import { beforeEach, describe, expect, it, vi } from "vitest";
import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import { getDashboardStats } from "@/lib/dashboard/actions";

vi.mock("@/lib/axios-instance", () => ({
  default: {
    get: vi.fn(),
  },
}));

describe("dashboard api actions", () => {
  beforeEach(() => {
    vi.mocked(axiosInstance.get).mockReset();
  });

  it("loads dashboard stats from the centralized endpoint", async () => {
    const data = {
      success: true,
      message: "OK",
      data: {
        total_items: 12,
        total_categories: 4,
        total_suppliers: 3,
        low_stock_count: 2,
        total_stock_value: 1200,
        recent_items: [],
        low_stock_items: [],
      },
    };

    vi.mocked(axiosInstance.get).mockResolvedValue({ data });

    await expect(getDashboardStats()).resolves.toEqual(data);
    expect(axiosInstance.get).toHaveBeenCalledWith(API_PATHS.DASHBOARD.STATS);
  });
});
