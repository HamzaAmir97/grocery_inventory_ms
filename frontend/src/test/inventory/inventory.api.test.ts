import { beforeEach, describe, expect, it, vi } from "vitest";
import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import {
  createInventoryItem,
  deleteInventoryItem,
  getInventoryItem,
  getInventoryItems,
  updateInventoryItem,
} from "@/lib/inventory/actions";
import type { ItemFormValues } from "@/types";

vi.mock("@/lib/axios-instance", () => ({
  default: {
    delete: vi.fn(),
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
  },
}));

const itemPayload: ItemFormValues = {
  name: "Basmati Rice",
  sku: "GR-RICE-001",
  category_id: 1,
  subcategory_id: 2,
  unit_id: 3,
  supplier_id: 4,
  price: 12.5,
  stock_quantity: 20,
  low_stock_threshold: 5,
  description: "Premium rice",
  is_active: true,
};

describe("inventory api actions", () => {
  beforeEach(() => {
    vi.mocked(axiosInstance.delete).mockReset();
    vi.mocked(axiosInstance.get).mockReset();
    vi.mocked(axiosInstance.post).mockReset();
    vi.mocked(axiosInstance.put).mockReset();
  });

  it("gets inventory items with filter params", async () => {
    const filters = { search: "rice", page: 2, low_stock: true };
    const data = {
      success: true,
      message: "OK",
      data: [],
      meta: { current_page: 2, per_page: 10, total: 0, last_page: 1 },
    };

    vi.mocked(axiosInstance.get).mockResolvedValue({ data });

    await expect(getInventoryItems(filters)).resolves.toEqual(data);
    expect(axiosInstance.get).toHaveBeenCalledWith(API_PATHS.INVENTORY.LIST, { params: filters });
  });

  it("gets one inventory item using an encoded id path", async () => {
    const data = { success: true, message: "OK", data: { ...itemPayload, id: 1 } };

    vi.mocked(axiosInstance.get).mockResolvedValue({ data });

    await expect(getInventoryItem("SKU/001")).resolves.toEqual(data);
    expect(axiosInstance.get).toHaveBeenCalledWith(API_PATHS.INVENTORY.DETAIL("SKU/001"));
  });

  it("creates, updates, and deletes inventory items through API_PATHS", async () => {
    const itemData = { success: true, message: "OK", data: { ...itemPayload, id: 1 } };
    const deleteData = { success: true, message: "Deleted.", data: null };

    vi.mocked(axiosInstance.post).mockResolvedValueOnce({ data: itemData });
    vi.mocked(axiosInstance.put).mockResolvedValueOnce({ data: itemData });
    vi.mocked(axiosInstance.delete).mockResolvedValueOnce({ data: deleteData });

    await expect(createInventoryItem(itemPayload)).resolves.toEqual(itemData);
    await expect(updateInventoryItem(1, itemPayload)).resolves.toEqual(itemData);
    await expect(deleteInventoryItem(1)).resolves.toEqual(deleteData);

    expect(axiosInstance.post).toHaveBeenCalledWith(API_PATHS.INVENTORY.CREATE, itemPayload);
    expect(axiosInstance.put).toHaveBeenCalledWith(API_PATHS.INVENTORY.UPDATE(1), itemPayload);
    expect(axiosInstance.delete).toHaveBeenCalledWith(API_PATHS.INVENTORY.DELETE(1));
  });
});
