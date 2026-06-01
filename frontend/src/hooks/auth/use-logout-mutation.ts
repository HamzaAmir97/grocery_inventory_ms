"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { logoutMutationOptions } from "@/lib/auth/actions";

export function useLogoutMutation() {
  const queryClient = useQueryClient();

  return useMutation(logoutMutationOptions(queryClient));
}
