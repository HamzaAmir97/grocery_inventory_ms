import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import type { ApiSuccessResponse, Category, CategoryPayload, PaginatedResponse, SettingsListFilters } from "@/types";

export async function getCategories(filters: SettingsListFilters = {}) {
  const response = await axiosInstance.get<PaginatedResponse<Category>>(API_PATHS.SETTINGS.CATEGORIES.LIST, {
    params: filters,
  });

  return response.data;
}

export async function getCategory(id: string | number) {
  const response = await axiosInstance.get<ApiSuccessResponse<Category>>(API_PATHS.SETTINGS.CATEGORIES.DETAIL(id));

  return response.data;
}

export async function createCategory(payload: CategoryPayload) {
  const response = await axiosInstance.post<ApiSuccessResponse<Category>>(API_PATHS.SETTINGS.CATEGORIES.CREATE, payload);

  return response.data;
}

export async function updateCategory(id: string | number, payload: CategoryPayload) {
  const response = await axiosInstance.put<ApiSuccessResponse<Category>>(API_PATHS.SETTINGS.CATEGORIES.UPDATE(id), payload);

  return response.data;
}

export async function deleteCategory(id: string | number) {
  const response = await axiosInstance.delete<ApiSuccessResponse<null>>(API_PATHS.SETTINGS.CATEGORIES.DELETE(id));

  return response.data;
}
