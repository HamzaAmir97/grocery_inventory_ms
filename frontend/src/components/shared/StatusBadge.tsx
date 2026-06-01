import { Pill } from "./Pill";

export function StatusBadge({ active }: { active: boolean }) {
  return <Pill tone={active ? "success" : "neutral"}>{active ? "Active" : "Inactive"}</Pill>;
}
