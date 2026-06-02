"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { updateUnitMutationOptions } from "@/lib/settings/actions";

export function useUpdateUnitMutation() {
  const queryClient = useQueryClient();

  return useMutation(updateUnitMutationOptions(queryClient));
}
