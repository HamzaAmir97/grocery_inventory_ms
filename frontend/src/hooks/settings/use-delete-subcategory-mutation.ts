"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { deleteSubcategoryMutationOptions } from "@/lib/settings/actions";

export function useDeleteSubcategoryMutation() {
  const queryClient = useQueryClient();

  return useMutation(deleteSubcategoryMutationOptions(queryClient));
}
