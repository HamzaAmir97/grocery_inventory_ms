"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { updateInventoryItemMutationOptions } from "@/lib/inventory/actions";

export function useUpdateItemMutation() {
  const queryClient = useQueryClient();

  return useMutation(updateInventoryItemMutationOptions(queryClient));
}
