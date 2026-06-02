export type SettingBase = {
  id: number;
  name: string;
  description?: string | null;
  is_active: boolean;
  // Optional usage counts returned by the API for delete-guard messaging.
  items_count?: number;
};

export type Category = SettingBase & {
  subcategories_count?: number;
};

export type Subcategory = SettingBase & {
  category_id: number;
  category?: Category;
};

export type Unit = SettingBase & {
  symbol?: string | null;
};

export type Supplier = SettingBase & {
  contact_person?: string | null;
  email?: string | null;
  phone?: string | null;
  address?: string | null;
};

export type LookupOption = {
  id: number;
  name: string;
};

export type SettingsListFilters = {
  search?: string;
  status?: "active" | "inactive";
  category_id?: number;
  sort_by?: "name" | "description" | "is_active" | "subcategories_count" | "items_count" | "category" | "symbol" | "contact_person" | "phone" | "email" | "created_at";
  sort_dir?: "asc" | "desc";
  page?: number;
  per_page?: number;
};
