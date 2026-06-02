"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { deleteCategoryMutationOptions } from "@/lib/settings/actions";

export function useDeleteCategoryMutation() {
  const queryClient = useQueryClient();

  return useMutation(deleteCategoryMutationOptions(queryClient));
}
