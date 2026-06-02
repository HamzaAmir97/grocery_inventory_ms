"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { updateCategoryMutationOptions } from "@/lib/settings/actions";

export function useUpdateCategoryMutation() {
  const queryClient = useQueryClient();

  return useMutation(updateCategoryMutationOptions(queryClient));
}
