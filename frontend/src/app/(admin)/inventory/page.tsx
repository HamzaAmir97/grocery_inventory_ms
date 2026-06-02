import { Suspense } from "react";
import { InventoryPageContent } from "@/components/inventory";

export default function InventoryPage() {
  return (
    <Suspense fallback={null}>
      <InventoryPageContent />
    </Suspense>
  );
}
