"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { deleteSupplierMutationOptions } from "@/lib/settings/actions";

export function useDeleteSupplierMutation() {
  const queryClient = useQueryClient();

  return useMutation(deleteSupplierMutationOptions(queryClient));
}
