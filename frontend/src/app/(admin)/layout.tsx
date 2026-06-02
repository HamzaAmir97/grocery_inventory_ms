import type { ReactNode } from "react";
import { DashboardLayout } from "@/components/layout";

export default function AdminLayout({
  children,
}: Readonly<{
  children: ReactNode;
}>) {
  return <DashboardLayout>{children}</DashboardLayout>;
}
