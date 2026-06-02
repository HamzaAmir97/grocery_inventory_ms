import type { ReactNode } from "react";
import { ROUTES, type NavIcon } from "@/constants";
import {
  IconBox,
  IconCategory,
  IconDashboard,
  IconRuler,
  IconTruck,
} from "@/components/shared";
import { getInventoryItems } from "@/lib/inventory/actions";
import { getCategories, getSubcategories, getSuppliers, getUnits } from "@/lib/settings/actions";
import type { AuthUser, Category, InventoryItem, Subcategory, Supplier, Unit } from "@/types";

export const ICONS: Record<NavIcon, (props: { size?: number }) => ReactNode> = {
  dashboard: (props) => <IconDashboard {...props} />,
  box: (props) => <IconBox {...props} />,
  category: (props) => <IconCategory {...props} />,
  ruler: (props) => <IconRuler {...props} />,
  truck: (props) => <IconTruck {...props} />,
};

export function userInitials(user?: AuthUser | null) {
  if (!user?.name) return "AU";

  return user.name
    .split(" ")
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0])
    .join("")
    .toUpperCase();
}

export function userShortName(user?: AuthUser | null) {
  const name = user?.name?.trim();

  if (!name) return "Admin";

  const parts = name.split(/\s+/);
  if (parts.length === 1) return parts[0];

  return `${parts[0]} ${parts[1][0]}.`;
}

export function pathMatches(pathname: string, href: string) {
  if (href === ROUTES.dashboard) return pathname === href;
  return pathname === href || pathname.startsWith(`${href}/`);
}

export type GlobalSearchType = "item" | "category" | "subcategory" | "unit" | "supplier";

export type GlobalSearchResult = {
  id: string;
  type: GlobalSearchType;
  label: string;
  title: string;
  subtitle: string;
  href: string;
};

export const GLOBAL_SEARCH_LIMIT = 5;

export function searchHref(route: string, value: string) {
  return `${route}?search=${encodeURIComponent(value)}`;
}

function joinSubtitle(parts: Array<string | null | undefined>) {
  return parts.filter(Boolean).join(" / ");
}

function mapItems(items: InventoryItem[]): GlobalSearchResult[] {
  return items.map((item) => ({
    id: `item-${item.id}`,
    type: "item",
    label: "Item",
    title: item.name,
    subtitle: joinSubtitle(["Item", item.sku, item.category?.name, item.supplier?.name]),
    href: searchHref(ROUTES.inventory, item.name),
  }));
}

function mapCategories(categories: Category[]): GlobalSearchResult[] {
  return categories.map((category) => ({
    id: `category-${category.id}`,
    type: "category",
    label: "Category",
    title: category.name,
    subtitle: joinSubtitle(["Category", category.description]),
    href: searchHref(ROUTES.settingsCategories, category.name),
  }));
}

function mapSubcategories(subcategories: Subcategory[]): GlobalSearchResult[] {
  return subcategories.map((subcategory) => ({
    id: `subcategory-${subcategory.id}`,
    type: "subcategory",
    label: "Subcategory",
    title: subcategory.name,
    subtitle: joinSubtitle(["Subcategory", subcategory.category?.name]),
    href: searchHref(ROUTES.settingsSubcategories, subcategory.name),
  }));
}

function mapUnits(units: Unit[]): GlobalSearchResult[] {
  return units.map((unit) => ({
    id: `unit-${unit.id}`,
    type: "unit",
    label: "Unit",
    title: unit.name,
    subtitle: joinSubtitle(["Unit", unit.symbol]),
    href: searchHref(ROUTES.settingsUnits, unit.name),
  }));
}

function mapSuppliers(suppliers: Supplier[]): GlobalSearchResult[] {
  return suppliers.map((supplier) => ({
    id: `supplier-${supplier.id}`,
    type: "supplier",
    label: "Supplier",
    title: supplier.name,
    subtitle: joinSubtitle(["Supplier", supplier.contact_person, supplier.email, supplier.phone]),
    href: searchHref(ROUTES.settingsSuppliers, supplier.name),
  }));
}

export async function searchGlobalRecords(search: string): Promise<GlobalSearchResult[]> {
  const term = search.trim();
  if (term.length < 2) return [];

  const [items, categories, subcategories, units, suppliers] = await Promise.all([
    getInventoryItems({ search: term, per_page: GLOBAL_SEARCH_LIMIT }),
    getCategories({ search: term, per_page: GLOBAL_SEARCH_LIMIT }),
    getSubcategories({ search: term, per_page: GLOBAL_SEARCH_LIMIT }),
    getUnits({ search: term, per_page: GLOBAL_SEARCH_LIMIT }),
    getSuppliers({ search: term, per_page: GLOBAL_SEARCH_LIMIT }),
  ]);

  return [
    ...mapItems(items.data),
    ...mapCategories(categories.data),
    ...mapSubcategories(subcategories.data),
    ...mapUnits(units.data),
    ...mapSuppliers(suppliers.data),
  ];
}
