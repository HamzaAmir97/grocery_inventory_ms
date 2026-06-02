import { beforeEach, describe, expect, it, vi } from "vitest";
import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import {
  getCategoryOptions,
  getSubcategoryOptions,
  getSupplierOptions,
  getUnitOptions,
} from "@/lib/lookups/actions";

vi.mock("@/lib/axios-instance", () => ({
  default: {
    get: vi.fn(),
  },
}));

describe("lookup api actions", () => {
  beforeEach(() => {
    vi.mocked(axiosInstance.get).mockReset();
  });

  it("loads category, unit, and supplier lookup data from lookup endpoints", async () => {
    const data = { success: true, message: "OK", data: [{ id: 1, name: "Fresh Produce" }] };

    vi.mocked(axiosInstance.get).mockResolvedValue({ data });

    await expect(getCategoryOptions()).resolves.toEqual(data);
    await expect(getUnitOptions()).resolves.toEqual(data);
    await expect(getSupplierOptions()).resolves.toEqual(data);

    expect(axiosInstance.get).toHaveBeenNthCalledWith(1, API_PATHS.LOOKUPS.CATEGORIES);
    expect(axiosInstance.get).toHaveBeenNthCalledWith(2, API_PATHS.LOOKUPS.UNITS);
    expect(axiosInstance.get).toHaveBeenNthCalledWith(3, API_PATHS.LOOKUPS.SUPPLIERS);
  });

  it("passes category_id only when loading dependent subcategories for a category", async () => {
    const data = { success: true, message: "OK", data: [] };

    vi.mocked(axiosInstance.get).mockResolvedValue({ data });

    await expect(getSubcategoryOptions()).resolves.toEqual(data);
    await expect(getSubcategoryOptions(9)).resolves.toEqual(data);

    expect(axiosInstance.get).toHaveBeenNthCalledWith(1, API_PATHS.LOOKUPS.SUBCATEGORIES, {
      params: undefined,
    });
    expect(axiosInstance.get).toHaveBeenNthCalledWith(2, API_PATHS.LOOKUPS.SUBCATEGORIES, {
      params: { category_id: 9 },
    });
  });
});
