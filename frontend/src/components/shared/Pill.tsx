import type { ReactNode } from "react";
import { Badge as ShadcnBadge } from "@/components/ui/badge";

export type PillTone = "success" | "warning" | "danger" | "info" | "neutral" | "accent";

const PILL_TONES: Record<PillTone, { bg: string; fg: string }> = {
  success: { bg: "var(--color-success-soft)", fg: "var(--color-success)" },
  warning: { bg: "var(--color-warning-soft)", fg: "var(--color-warning)" },
  danger: { bg: "var(--color-danger-soft)", fg: "var(--color-danger)" },
  info: { bg: "var(--color-info-soft)", fg: "var(--color-info)" },
  neutral: { bg: "var(--color-surface-3)", fg: "var(--color-fg-muted)" },
  accent: { bg: "var(--color-accent-soft)", fg: "var(--color-accent-press)" },
};

export function Pill({ tone = "neutral", dot = true, children }: { tone?: PillTone; dot?: boolean; children: ReactNode }) {
  const t = PILL_TONES[tone];
  const variant = tone === "danger" ? "destructive" : tone === "neutral" ? "secondary" : "default";

  return (
    <ShadcnBadge
      variant={variant}
      className="pill"
      style={{ background: t.bg, color: t.fg }}
    >
      {dot ? <span className="pill-dot" style={{ background: t.fg }} /> : null}
      {children}
    </ShadcnBadge>
  );
}
