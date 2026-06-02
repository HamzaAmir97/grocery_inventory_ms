"use client";

import { ItemWizard } from "./ItemWizard";

export function EditItemPageContent({ itemId }: { itemId: string }) {
  return <ItemWizard mode="edit" itemId={itemId} title="Edit item" />;
}
