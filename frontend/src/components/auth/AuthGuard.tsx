"use client";

import { useEffect, useState, type ReactNode } from "react";
import { useRouter } from "next/navigation";
import { ROUTES } from "@/constants";
import { getAuthToken } from "@/lib/auth/helpers";

const AUTH_CHECK_ENABLED = true;

export function AuthGuard({ children }: { children: ReactNode }) {
  const router = useRouter();
  const [ready, setReady] = useState(false);

  useEffect(() => {
    if (!AUTH_CHECK_ENABLED || Boolean(getAuthToken())) {
      const timeoutId = window.setTimeout(() => setReady(true), 0);
      return () => window.clearTimeout(timeoutId);
    }

    router.replace(ROUTES.login);
  }, [router]);

  if (!ready) {
    return (
      <div style={{ display: "grid", minHeight: "100vh", placeItems: "center", color: "var(--color-fg-muted)", fontSize: 14 }}>
        Checking session...
      </div>
    );
  }

  return <>{children}</>;
}
