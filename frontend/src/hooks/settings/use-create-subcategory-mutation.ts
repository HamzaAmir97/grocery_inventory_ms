"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { createSubcategoryMutationOptions } from "@/lib/settings/actions";

export function useCreateSubcategoryMutation() {
  const queryClient = useQueryClient();

  return useMutation(createSubcategoryMutationOptions(queryClient));
}
