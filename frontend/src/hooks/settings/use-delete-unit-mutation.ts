"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { deleteUnitMutationOptions } from "@/lib/settings/actions";

export function useDeleteUnitMutation() {
  const queryClient = useQueryClient();

  return useMutation(deleteUnitMutationOptions(queryClient));
}
