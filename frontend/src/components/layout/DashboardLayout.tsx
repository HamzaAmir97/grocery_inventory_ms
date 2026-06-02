"use client";

import type { CSSProperties, ReactNode } from "react";
import { AuthGuard } from "@/components/auth";
import { SidebarInset, SidebarProvider } from "@/components/ui/sidebar";
import { useCurrentUserQuery } from "@/hooks/auth";
import { useDashboardQuery } from "@/hooks/dashboard";
import { Sidebar } from "./Sidebar";
import { Topbar } from "./Topbar";

export function DashboardLayout({
  children,
  topbarMode = "always",
}: {
  children: ReactNode;
  topbarMode?: "always" | "mobile-only";
}) {
  const currentUserQuery = useCurrentUserQuery();
  const dashboardQuery = useDashboardQuery();
  const user = currentUserQuery.data?.data ?? null;
  const firstName = user?.name?.split(" ")[0] ?? "Admin";
  const lowStockItems = dashboardQuery.data?.data?.low_stock_items ?? 0;

  return (
    <AuthGuard>
      <SidebarProvider
        className="inventory-layout-shell"
        style={{ "--sidebar-width": "230px", "--sidebar-width-icon": "76px" } as CSSProperties}
      >
        <Sidebar user={user} />
        <SidebarInset className="inventory-layout-main">
        <Topbar
          title={`Welcome back, ${firstName}`}
          subtitle={`${lowStockItems} ${lowStockItems === 1 ? "item is" : "items are"} below threshold today.`}
          user={user}
          className={topbarMode === "mobile-only" ? "topbar-mobile-only" : ""}
        />
        <main className="page-shell">{children}</main>
        </SidebarInset>
      </SidebarProvider>
    </AuthGuard>
  );
}
