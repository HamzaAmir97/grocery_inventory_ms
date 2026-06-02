"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { updateSubcategoryMutationOptions } from "@/lib/settings/actions";

export function useUpdateSubcategoryMutation() {
  const queryClient = useQueryClient();

  return useMutation(updateSubcategoryMutationOptions(queryClient));
}
