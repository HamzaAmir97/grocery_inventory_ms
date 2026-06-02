import type { Category, Subcategory, Supplier, Unit } from "../settings";

export type Item = {
  id: number;
  name: string;
  sku: string;
  category_id: number;
  subcategory_id: number;
  unit_id: number;
  supplier_id: number;
  price: number;
  stock_quantity: number;
  low_stock_threshold: number;
  description?: string | null;
  is_active: boolean;
  category?: Category;
  subcategory?: Subcategory;
  unit?: Unit;
  supplier?: Supplier;
};

export type ItemFormValues = Omit<Item, "id" | "category" | "subcategory" | "unit" | "supplier">;

export type ItemFilters = {
  search?: string;
  category_id?: number;
  subcategory_id?: number;
  supplier_id?: number;
  unit_id?: number;
  low_stock?: boolean;
  sort_by?: "name" | "sku" | "category" | "subcategory" | "unit" | "supplier" | "price" | "stock_quantity" | "created_at";
  sort_dir?: "asc" | "desc";
  page?: number;
  per_page?: number;
};
