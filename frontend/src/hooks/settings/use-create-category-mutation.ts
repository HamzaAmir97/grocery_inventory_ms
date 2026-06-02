"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { createCategoryMutationOptions } from "@/lib/settings/actions";

export function useCreateCategoryMutation() {
  const queryClient = useQueryClient();

  return useMutation(createCategoryMutationOptions(queryClient));
}
