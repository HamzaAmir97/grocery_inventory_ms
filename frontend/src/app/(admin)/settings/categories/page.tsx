import { Suspense } from "react";
import { CategoriesPageContent } from "@/components/settings";

export default function CategoriesPage() {
  return (
    <Suspense fallback={null}>
      <CategoriesPageContent />
    </Suspense>
  );
}
