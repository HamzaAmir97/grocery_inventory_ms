import { Avatar } from "@/components/shared";
import { initials } from "@/lib/format";

export function NameCell({ name }: { name: string }) {
  return (
    <div style={{ display: "flex", alignItems: "center", gap: 10 }}>
      <Avatar initials={initials(name)} size={28} />
      <span style={{ fontWeight: 600 }}>{name}</span>
    </div>
  );
}
