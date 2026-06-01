import { afterEach, describe, expect, it, vi } from "vitest";
import { AUTH_TOKEN_KEY, clearAuthToken, getAuthToken, setAuthToken } from "@/lib/auth/helpers";

function createLocalStorageMock() {
  const store = new Map<string, string>();

  return {
    getItem: vi.fn((key: string) => store.get(key) ?? null),
    removeItem: vi.fn((key: string) => {
      store.delete(key);
    }),
    setItem: vi.fn((key: string, value: string) => {
      store.set(key, value);
    }),
  };
}

describe("auth storage helper", () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it("returns null when used outside the browser", () => {
    vi.stubGlobal("window", undefined);

    expect(getAuthToken()).toBeNull();
  });

  it("sets, trims, reads, and clears the stored token", () => {
    const localStorage = createLocalStorageMock();

    vi.stubGlobal("window", { localStorage });

    setAuthToken("  secure-token  ");

    expect(localStorage.setItem).toHaveBeenCalledWith(AUTH_TOKEN_KEY, "  secure-token  ");
    expect(getAuthToken()).toBe("secure-token");

    clearAuthToken();

    expect(localStorage.removeItem).toHaveBeenCalledWith(AUTH_TOKEN_KEY);
    expect(getAuthToken()).toBeNull();
  });
});
