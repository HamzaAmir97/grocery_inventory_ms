"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { createSupplierMutationOptions } from "@/lib/settings/actions";

export function useCreateSupplierMutation() {
  const queryClient = useQueryClient();

  return useMutation(createSupplierMutationOptions(queryClient));
}
