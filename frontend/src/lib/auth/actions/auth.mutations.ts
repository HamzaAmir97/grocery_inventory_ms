import type { QueryClient } from "@tanstack/react-query";
import { clearAuthToken, setAuthToken } from "@/lib/auth/helpers";
import { loginUser, logoutUser } from "./auth.api";
import { authKeys } from "./auth.keys";

export function loginMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: loginUser,
    onSuccess: (response: Awaited<ReturnType<typeof loginUser>>) => {
      setAuthToken(response.data.token);
      queryClient.setQueryData(authKeys.currentUser(), {
        success: true,
        message: response.message,
        data: response.data.user,
      });
    },
  };
}

export function logoutMutationOptions(queryClient: QueryClient) {
  return {
    mutationFn: logoutUser,
    onSettled: () => {
      clearAuthToken();
      queryClient.removeQueries({ queryKey: authKeys.all });
    },
  };
}
