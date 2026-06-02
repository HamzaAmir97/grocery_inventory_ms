import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import type { ApiSuccessResponse, PaginatedResponse, SettingsListFilters, Unit, UnitPayload } from "@/types";

export async function getUnits(filters: SettingsListFilters = {}) {
  const response = await axiosInstance.get<PaginatedResponse<Unit>>(API_PATHS.SETTINGS.UNITS.LIST, {
    params: filters,
  });

  return response.data;
}

export async function getUnit(id: string | number) {
  const response = await axiosInstance.get<ApiSuccessResponse<Unit>>(API_PATHS.SETTINGS.UNITS.DETAIL(id));

  return response.data;
}

export async function createUnit(payload: UnitPayload) {
  const response = await axiosInstance.post<ApiSuccessResponse<Unit>>(API_PATHS.SETTINGS.UNITS.CREATE, payload);

  return response.data;
}

export async function updateUnit(id: string | number, payload: UnitPayload) {
  const response = await axiosInstance.put<ApiSuccessResponse<Unit>>(API_PATHS.SETTINGS.UNITS.UPDATE(id), payload);

  return response.data;
}

export async function deleteUnit(id: string | number) {
  const response = await axiosInstance.delete<ApiSuccessResponse<null>>(API_PATHS.SETTINGS.UNITS.DELETE(id));

  return response.data;
}
