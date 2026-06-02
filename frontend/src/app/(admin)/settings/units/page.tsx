import { Suspense } from "react";
import { UnitsPageContent } from "@/components/settings";

export default function UnitsPage() {
  return (
    <Suspense fallback={null}>
      <UnitsPageContent />
    </Suspense>
  );
}
