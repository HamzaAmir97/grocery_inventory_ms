"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { loginMutationOptions } from "@/lib/auth/actions";

export function useLoginMutation() {
  const queryClient = useQueryClient();

  return useMutation(loginMutationOptions(queryClient));
}
