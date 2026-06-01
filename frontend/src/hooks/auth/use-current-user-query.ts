"use client";

import { useQuery } from "@tanstack/react-query";
import { currentUserQueryOptions } from "@/lib/auth/actions";

export function useCurrentUserQuery() {
  return useQuery(currentUserQueryOptions());
}
