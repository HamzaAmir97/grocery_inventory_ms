"use client";

import { useMutation, useQueryClient } from "@tanstack/react-query";
import { deleteInventoryItemMutationOptions } from "@/lib/inventory/actions";

export function useDeleteItemMutation() {
  const queryClient = useQueryClient();

  return useMutation(deleteInventoryItemMutationOptions(queryClient));
}
