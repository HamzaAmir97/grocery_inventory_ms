export type DashboardItemStatusTone = "success" | "warning" | "danger";

export type DashboardItem = {
  id: number;
  name: string;
  sku: string | null;
  category?: string | null;
  supplier?: string | null;
  unit_symbol?: string | null;
  stock_quantity: number;
  low_stock_threshold: number;
  price: number | string;
  status: "in_stock" | "low_stock" | "out_of_stock";
  status_label: string;
  status_tone: DashboardItemStatusTone;
};

export type DashboardSummaryCard = {
  key: "total_items" | "categories" | "suppliers" | "low_stock";
  label: string;
  value: number;
  badge: string;
  badge_tone: DashboardItemStatusTone;
};

export type DashboardGrowthPoint = {
  month: string;
  count: number;
};

export type DashboardCategoryBreakdownItem = {
  name: string;
  items_count: number;
  percentage: number;
};

export type DashboardStats = {
  total_items: number;
  total_categories: number;
  total_suppliers: number;
  low_stock_items: number;
  total_stock_value: number | string;
  summary_cards: DashboardSummaryCard[];
  inventory_growth_year: number;
  inventory_growth: DashboardGrowthPoint[];
  category_breakdown: {
    total: number;
    items: DashboardCategoryBreakdownItem[];
  };
  recent_items: DashboardItem[];
  low_stock_list: DashboardItem[];
};
