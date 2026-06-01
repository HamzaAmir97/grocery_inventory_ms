import type { ReactNode } from "react";
import { Pill, type PillTone } from "./Pill";

// Back-compat tone names mapped onto the design palette.
export function Badge({ children, tone = "slate" }: { children: ReactNode; tone?: "slate" | "green" | "red" | "amber" | "blue" }) {
  const map: Record<string, PillTone> = { slate: "neutral", green: "success", red: "danger", amber: "warning", blue: "info" };
  return <Pill tone={map[tone] ?? "neutral"}>{children}</Pill>;
}
