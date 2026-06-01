import type { ReactNode } from "react";
import { Card as ShadcnCard } from "@/components/ui/card";
import { IconAlert } from "./icons";

export function EmptyState({ icon, title, message, action }: { icon?: ReactNode; title: string; message: string; action?: ReactNode }) {
  return (
    <ShadcnCard
      className="empty-state"
      style={{
        border: "1.5px dashed var(--color-border-strong)",
        borderRadius: 14,
        padding: 32,
        textAlign: "center",
        display: "flex",
        flexDirection: "column",
        alignItems: "center",
        gap: 12,
        background: "var(--color-surface-2)",
      }}
    >
      <div
        style={{
          width: 52,
          height: 52,
          borderRadius: 14,
          background: "var(--color-accent-soft)",
          color: "var(--color-accent-press)",
          display: "flex",
          alignItems: "center",
          justifyContent: "center",
        }}
      >
        {icon ?? <IconAlert size={24} />}
      </div>
      <div style={{ fontSize: 16, fontWeight: 700 }}>{title}</div>
      <div style={{ fontSize: 13, color: "var(--color-fg-muted)", maxWidth: 360 }}>{message}</div>
      {action}
    </ShadcnCard>
  );
}
