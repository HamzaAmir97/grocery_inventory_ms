"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { updateSupplierMutationOptions } from "@/lib/settings/actions";

export function useUpdateSupplierMutation() {
  const queryClient = useQueryClient();

  return useMutation(updateSupplierMutationOptions(queryClient));
}
