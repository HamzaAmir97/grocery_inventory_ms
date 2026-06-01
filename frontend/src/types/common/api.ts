export type ApiSuccessResponse<T> = {
  success: boolean;
  message: string;
  data: T;
};

export type ApiResponse<T> = ApiSuccessResponse<T>;

export type ApiErrorResponse = {
  success: false;
  message: string;
  errors?: Record<string, string[]>;
};

export type ApiError = ApiErrorResponse;

export type PaginatedResponse<T> = ApiSuccessResponse<T[]> & {
  meta: {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
  };
};

export type PaginatedApiResponse<T> = PaginatedResponse<T>;
