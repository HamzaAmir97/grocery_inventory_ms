import { IconBox, IconCategory, IconRuler, IconTruck } from "@/components/shared";
import type { GlobalSearchType } from "./helpers";

export function GlobalSearchIcon({ type }: { type: GlobalSearchType }) {
  if (type === "supplier") return <IconTruck size={15} />;
  if (type === "unit") return <IconRuler size={15} />;
  if (type === "item") return <IconBox size={15} />;
  return <IconCategory size={15} />;
}
