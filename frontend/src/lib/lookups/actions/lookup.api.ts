import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import type { ApiSuccessResponse, LookupOption } from "@/types";

export async function getCategoryOptions() {
  const response = await axiosInstance.get<ApiSuccessResponse<LookupOption[]>>(API_PATHS.LOOKUPS.CATEGORIES);

  return response.data;
}

export async function getSubcategoryOptions(categoryId?: number) {
  const response = await axiosInstance.get<ApiSuccessResponse<LookupOption[]>>(API_PATHS.LOOKUPS.SUBCATEGORIES, {
    params: categoryId ? { category_id: categoryId } : undefined,
  });

  return response.data;
}

export async function getUnitOptions() {
  const response = await axiosInstance.get<ApiSuccessResponse<LookupOption[]>>(API_PATHS.LOOKUPS.UNITS);

  return response.data;
}

export async function getSupplierOptions() {
  const response = await axiosInstance.get<ApiSuccessResponse<LookupOption[]>>(API_PATHS.LOOKUPS.SUPPLIERS);

  return response.data;
}
