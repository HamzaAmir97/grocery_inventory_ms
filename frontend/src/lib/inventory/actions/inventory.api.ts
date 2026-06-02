import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import type {
  ApiSuccessResponse,
  CreateItemPayload,
  InventoryFilters,
  InventoryItem,
  PaginatedResponse,
  UpdateItemPayload,
} from "@/types";

export async function getInventoryItems(filters: InventoryFilters = {}) {
  const response = await axiosInstance.get<PaginatedResponse<InventoryItem>>(API_PATHS.INVENTORY.LIST, {
    params: filters,
  });

  return response.data;
}

export async function getInventoryItem(id: string | number) {
  const response = await axiosInstance.get<ApiSuccessResponse<InventoryItem>>(API_PATHS.INVENTORY.DETAIL(id));

  return response.data;
}

export async function createInventoryItem(payload: CreateItemPayload) {
  const response = await axiosInstance.post<ApiSuccessResponse<InventoryItem>>(API_PATHS.INVENTORY.CREATE, payload);

  return response.data;
}

export async function updateInventoryItem(id: string | number, payload: UpdateItemPayload) {
  const response = await axiosInstance.put<ApiSuccessResponse<InventoryItem>>(API_PATHS.INVENTORY.UPDATE(id), payload);

  return response.data;
}

export async function deleteInventoryItem(id: string | number) {
  const response = await axiosInstance.delete<ApiSuccessResponse<null>>(API_PATHS.INVENTORY.DELETE(id));

  return response.data;
}
