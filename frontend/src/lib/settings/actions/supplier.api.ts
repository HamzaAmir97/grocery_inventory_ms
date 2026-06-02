import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import type { ApiSuccessResponse, PaginatedResponse, SettingsListFilters, Supplier, SupplierPayload } from "@/types";

export async function getSuppliers(filters: SettingsListFilters = {}) {
  const response = await axiosInstance.get<PaginatedResponse<Supplier>>(API_PATHS.SETTINGS.SUPPLIERS.LIST, {
    params: filters,
  });

  return response.data;
}

export async function getSupplier(id: string | number) {
  const response = await axiosInstance.get<ApiSuccessResponse<Supplier>>(API_PATHS.SETTINGS.SUPPLIERS.DETAIL(id));

  return response.data;
}

export async function createSupplier(payload: SupplierPayload) {
  const response = await axiosInstance.post<ApiSuccessResponse<Supplier>>(API_PATHS.SETTINGS.SUPPLIERS.CREATE, payload);

  return response.data;
}

export async function updateSupplier(id: string | number, payload: SupplierPayload) {
  const response = await axiosInstance.put<ApiSuccessResponse<Supplier>>(API_PATHS.SETTINGS.SUPPLIERS.UPDATE(id), payload);

  return response.data;
}

export async function deleteSupplier(id: string | number) {
  const response = await axiosInstance.delete<ApiSuccessResponse<null>>(API_PATHS.SETTINGS.SUPPLIERS.DELETE(id));

  return response.data;
}
