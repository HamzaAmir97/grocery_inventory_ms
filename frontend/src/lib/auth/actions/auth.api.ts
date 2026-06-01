import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import type { ApiSuccessResponse, AuthPayload, AuthUser, LoginPayload } from "@/types";

export async function loginUser(payload: LoginPayload) {
  const response = await axiosInstance.post<ApiSuccessResponse<AuthPayload>>(API_PATHS.AUTH.LOGIN, payload);

  return response.data;
}

export async function logoutUser() {
  const response = await axiosInstance.post<ApiSuccessResponse<null>>(API_PATHS.AUTH.LOGOUT);

  return response.data;
}

export async function getCurrentUser() {
  const response = await axiosInstance.get<ApiSuccessResponse<AuthUser>>(API_PATHS.AUTH.ME);

  return response.data;
}
