import type { CSSProperties, ReactNode } from "react";
import { Card as ShadcnCard } from "@/components/ui/card";
import { cn } from "@/lib/utils";

export function Card({ children, className = "", padded = true, style }: { children: ReactNode; className?: string; padded?: boolean; style?: CSSProperties }) {
  return (
    <ShadcnCard
      className={cn("card", padded && "card-pad", className)}
      style={style}
    >
      {children}
    </ShadcnCard>
  );
}
