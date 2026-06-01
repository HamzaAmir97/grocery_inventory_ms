import { beforeEach, describe, expect, it, vi } from "vitest";
import axiosInstance from "@/lib/axios-instance";
import { API_PATHS } from "@/lib/api-paths";
import { getCurrentUser, loginUser, logoutUser } from "@/lib/auth/actions";

vi.mock("@/lib/axios-instance", () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
  },
}));

describe("auth api actions", () => {
  beforeEach(() => {
    vi.mocked(axiosInstance.get).mockReset();
    vi.mocked(axiosInstance.post).mockReset();
  });

  it("posts login credentials and returns response data", async () => {
    const payload = { email: "admin@example.com", password: "password123" };
    const data = {
      success: true,
      message: "Logged in.",
      data: {
        token: "jwt-token",
        user: { id: 1, name: "Admin", email: "admin@example.com" },
      },
    };

    vi.mocked(axiosInstance.post).mockResolvedValue({ data });

    await expect(loginUser(payload)).resolves.toEqual(data);
    expect(axiosInstance.post).toHaveBeenCalledWith(API_PATHS.AUTH.LOGIN, payload);
  });

  it("posts logout without duplicating endpoint strings", async () => {
    const data = { success: true, message: "Logged out.", data: null };

    vi.mocked(axiosInstance.post).mockResolvedValue({ data });

    await expect(logoutUser()).resolves.toEqual(data);
    expect(axiosInstance.post).toHaveBeenCalledWith(API_PATHS.AUTH.LOGOUT);
  });

  it("gets the current user through the centralized auth path", async () => {
    const data = {
      success: true,
      message: "OK",
      data: { id: 1, name: "Admin", email: "admin@example.com" },
    };

    vi.mocked(axiosInstance.get).mockResolvedValue({ data });

    await expect(getCurrentUser()).resolves.toEqual(data);
    expect(axiosInstance.get).toHaveBeenCalledWith(API_PATHS.AUTH.ME);
  });
});
