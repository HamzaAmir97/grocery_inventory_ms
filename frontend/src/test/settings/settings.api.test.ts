import { beforeEach, describe, expect, it, vi } from "vitest";
import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import {
  createCategory,
  createSubcategory,
  createSupplier,
  createUnit,
  deleteCategory,
  deleteSubcategory,
  deleteSupplier,
  deleteUnit,
  getCategories,
  getCategory,
  getSubcategories,
  getSubcategory,
  getSuppliers,
  getSupplier,
  getUnit,
  getUnits,
  updateCategory,
  updateSubcategory,
  updateSupplier,
  updateUnit,
} from "@/lib/settings/actions";

vi.mock("@/lib/axios-instance", () => ({
  default: {
    delete: vi.fn(),
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
  },
}));

describe("settings api actions", () => {
  beforeEach(() => {
    vi.mocked(axiosInstance.delete).mockReset();
    vi.mocked(axiosInstance.get).mockReset();
    vi.mocked(axiosInstance.post).mockReset();
    vi.mocked(axiosInstance.put).mockReset();
  });

  it("uses centralized paths for category CRUD", async () => {
    const filters = { search: "fresh", status: "active" as const };
    const payload = { name: "Fresh Produce", description: null, is_active: true };
    const listData = {
      success: true,
      message: "OK",
      data: [],
      meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },
    };
    const entityData = { success: true, message: "OK", data: { ...payload, id: 1 } };
    const deleteData = { success: true, message: "Deleted.", data: null };

    vi.mocked(axiosInstance.get).mockResolvedValueOnce({ data: listData }).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.post).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.put).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.delete).mockResolvedValueOnce({ data: deleteData });

    await expect(getCategories(filters)).resolves.toEqual(listData);
    await expect(getCategory(1)).resolves.toEqual(entityData);
    await expect(createCategory(payload)).resolves.toEqual(entityData);
    await expect(updateCategory(1, payload)).resolves.toEqual(entityData);
    await expect(deleteCategory(1)).resolves.toEqual(deleteData);

    expect(axiosInstance.get).toHaveBeenNthCalledWith(1, API_PATHS.SETTINGS.CATEGORIES.LIST, { params: filters });
    expect(axiosInstance.get).toHaveBeenNthCalledWith(2, API_PATHS.SETTINGS.CATEGORIES.DETAIL(1));
    expect(axiosInstance.post).toHaveBeenCalledWith(API_PATHS.SETTINGS.CATEGORIES.CREATE, payload);
    expect(axiosInstance.put).toHaveBeenCalledWith(API_PATHS.SETTINGS.CATEGORIES.UPDATE(1), payload);
    expect(axiosInstance.delete).toHaveBeenCalledWith(API_PATHS.SETTINGS.CATEGORIES.DELETE(1));
  });

  it("uses centralized paths for subcategory CRUD", async () => {
    const filters = { category_id: 2 };
    const payload = { name: "Leafy Greens", category_id: 2, description: null, is_active: true };
    const listData = {
      success: true,
      message: "OK",
      data: [],
      meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },
    };
    const entityData = { success: true, message: "OK", data: { ...payload, id: 1 } };
    const deleteData = { success: true, message: "Deleted.", data: null };

    vi.mocked(axiosInstance.get).mockResolvedValueOnce({ data: listData }).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.post).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.put).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.delete).mockResolvedValueOnce({ data: deleteData });

    await expect(getSubcategories(filters)).resolves.toEqual(listData);
    await expect(getSubcategory(1)).resolves.toEqual(entityData);
    await expect(createSubcategory(payload)).resolves.toEqual(entityData);
    await expect(updateSubcategory(1, payload)).resolves.toEqual(entityData);
    await expect(deleteSubcategory(1)).resolves.toEqual(deleteData);

    expect(axiosInstance.get).toHaveBeenNthCalledWith(1, API_PATHS.SETTINGS.SUBCATEGORIES.LIST, { params: filters });
    expect(axiosInstance.get).toHaveBeenNthCalledWith(2, API_PATHS.SETTINGS.SUBCATEGORIES.DETAIL(1));
    expect(axiosInstance.post).toHaveBeenCalledWith(API_PATHS.SETTINGS.SUBCATEGORIES.CREATE, payload);
    expect(axiosInstance.put).toHaveBeenCalledWith(API_PATHS.SETTINGS.SUBCATEGORIES.UPDATE(1), payload);
    expect(axiosInstance.delete).toHaveBeenCalledWith(API_PATHS.SETTINGS.SUBCATEGORIES.DELETE(1));
  });

  it("uses centralized paths for unit CRUD", async () => {
    const payload = { name: "Kilogram", symbol: "kg", description: null, is_active: true };
    const listData = {
      success: true,
      message: "OK",
      data: [],
      meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },
    };
    const entityData = { success: true, message: "OK", data: { ...payload, id: 1 } };
    const deleteData = { success: true, message: "Deleted.", data: null };

    vi.mocked(axiosInstance.get).mockResolvedValueOnce({ data: listData }).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.post).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.put).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.delete).mockResolvedValueOnce({ data: deleteData });

    await expect(getUnits()).resolves.toEqual(listData);
    await expect(getUnit(1)).resolves.toEqual(entityData);
    await expect(createUnit(payload)).resolves.toEqual(entityData);
    await expect(updateUnit(1, payload)).resolves.toEqual(entityData);
    await expect(deleteUnit(1)).resolves.toEqual(deleteData);

    expect(axiosInstance.get).toHaveBeenNthCalledWith(1, API_PATHS.SETTINGS.UNITS.LIST, { params: {} });
    expect(axiosInstance.get).toHaveBeenNthCalledWith(2, API_PATHS.SETTINGS.UNITS.DETAIL(1));
    expect(axiosInstance.post).toHaveBeenCalledWith(API_PATHS.SETTINGS.UNITS.CREATE, payload);
    expect(axiosInstance.put).toHaveBeenCalledWith(API_PATHS.SETTINGS.UNITS.UPDATE(1), payload);
    expect(axiosInstance.delete).toHaveBeenCalledWith(API_PATHS.SETTINGS.UNITS.DELETE(1));
  });

  it("uses centralized paths for supplier CRUD", async () => {
    const payload = {
      name: "North Market",
      contact_person: "Nadia",
      email: "contact@north.example",
      phone: "555-0100",
      address: "North District",
      description: null,
      is_active: true,
    };
    const listData = {
      success: true,
      message: "OK",
      data: [],
      meta: { current_page: 1, per_page: 10, total: 0, last_page: 1 },
    };
    const entityData = { success: true, message: "OK", data: { ...payload, id: 1 } };
    const deleteData = { success: true, message: "Deleted.", data: null };

    vi.mocked(axiosInstance.get).mockResolvedValueOnce({ data: listData }).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.post).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.put).mockResolvedValueOnce({ data: entityData });
    vi.mocked(axiosInstance.delete).mockResolvedValueOnce({ data: deleteData });

    await expect(getSuppliers()).resolves.toEqual(listData);
    await expect(getSupplier(1)).resolves.toEqual(entityData);
    await expect(createSupplier(payload)).resolves.toEqual(entityData);
    await expect(updateSupplier(1, payload)).resolves.toEqual(entityData);
    await expect(deleteSupplier(1)).resolves.toEqual(deleteData);

    expect(axiosInstance.get).toHaveBeenNthCalledWith(1, API_PATHS.SETTINGS.SUPPLIERS.LIST, { params: {} });
    expect(axiosInstance.get).toHaveBeenNthCalledWith(2, API_PATHS.SETTINGS.SUPPLIERS.DETAIL(1));
    expect(axiosInstance.post).toHaveBeenCalledWith(API_PATHS.SETTINGS.SUPPLIERS.CREATE, payload);
    expect(axiosInstance.put).toHaveBeenCalledWith(API_PATHS.SETTINGS.SUPPLIERS.UPDATE(1), payload);
    expect(axiosInstance.delete).toHaveBeenCalledWith(API_PATHS.SETTINGS.SUPPLIERS.DELETE(1));
  });
});
