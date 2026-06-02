import { Suspense } from "react";
import { SubcategoriesPageContent } from "@/components/settings";

export default function SubcategoriesPage() {
  return (
    <Suspense fallback={null}>
      <SubcategoriesPageContent />
    </Suspense>
  );
}
