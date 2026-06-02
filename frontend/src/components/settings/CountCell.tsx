import { Pill } from "@/components/shared";
import { formatNumber } from "@/lib/format";

export function CountCell({ value, tone = "neutral" }: { value?: number; tone?: "neutral" | "accent" }) {
  return <Pill tone={tone} dot={false}>{formatNumber(value ?? 0)}</Pill>;
}
