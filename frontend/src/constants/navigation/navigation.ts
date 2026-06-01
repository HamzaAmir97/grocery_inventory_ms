import { ROUTES } from "../routes";

export type NavIcon = "dashboard" | "box" | "category" | "ruler" | "truck";

export type NavItem = { label: string; href: string; icon: NavIcon; nested?: boolean };

export type NavSection = { label: string; items: NavItem[] };

export const NAV_SECTIONS: NavSection[] = [
  {
    label: "Workspace",
    items: [
      { label: "Dashboard", href: ROUTES.dashboard, icon: "dashboard" },
      { label: "Inventory", href: ROUTES.inventory, icon: "box" },
    ],
  },
  {
    label: "Database",
    items: [
      { label: "Categories", href: ROUTES.settingsCategories, icon: "category", nested: true },
      { label: "Subcategories", href: ROUTES.settingsSubcategories, icon: "category", nested: true },
      { label: "Units", href: ROUTES.settingsUnits, icon: "ruler", nested: true },
      { label: "Suppliers", href: ROUTES.settingsSuppliers, icon: "truck", nested: true },
    ],
  },
];

// Flat list kept for any consumer that needs every destination.
export const NAVIGATION_ITEMS = NAV_SECTIONS.flatMap((section) => section.items);
