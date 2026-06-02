"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { createInventoryItemMutationOptions } from "@/lib/inventory/actions";

export function useCreateItemMutation() {
  const queryClient = useQueryClient();

  return useMutation(createInventoryItemMutationOptions(queryClient));
}
