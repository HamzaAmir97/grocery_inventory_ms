export type SupplierPayload = {
  name: string;
  contact_person?: string | null;
  email?: string | null;
  phone?: string | null;
  address?: string | null;
  description?: string | null;
  is_active: boolean;
};
