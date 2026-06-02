import { EditItemPageContent } from "@/components/inventory";

type EditInventoryItemPageProps = {
  params: Promise<{ id: string }>;
};

export default async function EditInventoryItemPage({ params }: EditInventoryItemPageProps) {
  const { id } = await params;
  return <EditItemPageContent itemId={id} />;
}
