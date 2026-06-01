import { queryOptions } from "@tanstack/react-query";
import { getCurrentUser } from "./auth.api";
import { authKeys } from "./auth.keys";

export function currentUserQueryOptions() {
  return queryOptions({
    queryKey: authKeys.currentUser(),
    queryFn: getCurrentUser,
    retry: false,
  });
}
