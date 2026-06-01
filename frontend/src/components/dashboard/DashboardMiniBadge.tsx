import type { ReactNode } from "react";
import { Pill } from "@/components/shared";
import type { DashboardItemStatusTone } from "@/types";

export function DashboardMiniBadge({
  tone,
  children,
}: {
  tone: DashboardItemStatusTone;
  children: ReactNode;
}) {
  return (
    <Pill tone={tone} dot={false}>
      {children}
    </Pill>
  );
}
