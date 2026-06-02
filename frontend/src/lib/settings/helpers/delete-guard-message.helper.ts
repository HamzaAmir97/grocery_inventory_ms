import type { SettingBase } from "@/types";

export function getDeleteGuardMessage(record: SettingBase) {
  return `"${record.name}" is currently used by inventory items or related records. Reassign or remove those first, then try again.`;
}
