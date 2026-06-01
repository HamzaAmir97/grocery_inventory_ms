import { Pill } from "./Pill";

export function LowStockBadge({ isLowStock }: { isLowStock: boolean }) {
  return <Pill tone={isLowStock ? "warning" : "success"}>{isLowStock ? "Low stock" : "In stock"}</Pill>;
}
