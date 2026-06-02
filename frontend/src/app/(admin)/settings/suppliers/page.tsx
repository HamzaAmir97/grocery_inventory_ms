import { Suspense } from "react";
import { SuppliersPageContent } from "@/components/settings";

export default function SuppliersPage() {
  return (
    <Suspense fallback={null}>
      <SuppliersPageContent />
    </Suspense>
  );
}
