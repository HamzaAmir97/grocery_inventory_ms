import axios, { AxiosError } from "axios";
import { clearAuthToken, getAuthToken } from "@/lib/auth/helpers";
import type { ApiErrorResponse } from "@/types";

export class ApiClientError extends Error {
  status?: number;
  errors?: Record<string, string[]>;

  constructor(message: string, status?: number, errors?: Record<string, string[]>) {
    super(message);
    this.name = "ApiClientError";
    this.status = status;
    this.errors = errors;
  }
}

function isApiErrorResponse(value: unknown): value is ApiErrorResponse {
  return Boolean(value && typeof value === "object" && "success" in value && (value as { success: unknown }).success === false);
}

const axiosInstance = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_BASE_URL ?? process.env.NEXT_PUBLIC_API_URL,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

axiosInstance.interceptors.request.use((config) => {
  const token = getAuthToken();

  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  if (typeof FormData !== "undefined" && config.data instanceof FormData) {
    delete config.headers["Content-Type"];
  }

  return config;
});

axiosInstance.interceptors.response.use(
  (response) => response,
  (error: AxiosError<unknown>) => {
    if (error.response?.status === 401) {
      clearAuthToken();

      if (typeof window !== "undefined" && window.location.pathname !== "/login") {
        window.location.assign("/login");
      }
    }

    const payload = error.response?.data;

    if (isApiErrorResponse(payload)) {
      return Promise.reject(new ApiClientError(payload.message, error.response?.status, payload.errors));
    }

    return Promise.reject(new ApiClientError(error.message || "Request failed.", error.response?.status));
  },
);

export default axiosInstance;
