"use client";

import { useEffect, useState, type ReactNode } from "react";
import { useRouter } from "next/navigation";
import { ROUTES } from "@/constants";
import { getAuthToken } from "@/lib/auth/helpers";

const AUTH_CHECK_ENABLED = true;

export function AuthGuard({ children }: { children: ReactNode }) {
  const router = useRouter();
  const [ready] = useState(() => !AUTH_CHECK_ENABLED || Boolean(getAuthToken()));

  useEffect(() => {
    if (AUTH_CHECK_ENABLED && !ready) {
      router.replace(ROUTES.login);
    }
  }, [ready, router]);

  if (!ready) {
    return (
      <div style={{ display: "grid", minHeight: "100vh", placeItems: "center", color: "var(--color-fg-muted)", fontSize: 14 }}>
        Checking session...
      </div>
    );
  }

  return <>{children}</>;
}
