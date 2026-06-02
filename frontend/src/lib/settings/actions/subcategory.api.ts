import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import type { ApiSuccessResponse, PaginatedResponse, SettingsListFilters, Subcategory, SubcategoryPayload } from "@/types";

export async function getSubcategories(filters: SettingsListFilters = {}) {
  const response = await axiosInstance.get<PaginatedResponse<Subcategory>>(API_PATHS.SETTINGS.SUBCATEGORIES.LIST, {
    params: filters,
  });

  return response.data;
}

export async function getSubcategory(id: string | number) {
  const response = await axiosInstance.get<ApiSuccessResponse<Subcategory>>(API_PATHS.SETTINGS.SUBCATEGORIES.DETAIL(id));

  return response.data;
}

export async function createSubcategory(payload: SubcategoryPayload) {
  const response = await axiosInstance.post<ApiSuccessResponse<Subcategory>>(API_PATHS.SETTINGS.SUBCATEGORIES.CREATE, payload);

  return response.data;
}

export async function updateSubcategory(id: string | number, payload: SubcategoryPayload) {
  const response = await axiosInstance.put<ApiSuccessResponse<Subcategory>>(API_PATHS.SETTINGS.SUBCATEGORIES.UPDATE(id), payload);

  return response.data;
}

export async function deleteSubcategory(id: string | number) {
  const response = await axiosInstance.delete<ApiSuccessResponse<null>>(API_PATHS.SETTINGS.SUBCATEGORIES.DELETE(id));

  return response.data;
}
