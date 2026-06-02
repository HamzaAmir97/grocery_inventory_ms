"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { createUnitMutationOptions } from "@/lib/settings/actions";

export function useCreateUnitMutation() {
  const queryClient = useQueryClient();

  return useMutation(createUnitMutationOptions(queryClient));
}
