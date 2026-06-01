// Number formatting per the design system: 1,240 not 1240; $3.50 not $3.5.

export function formatNumber(value: number): string {
  return new Intl.NumberFormat("en-US").format(value);
}

export function formatCurrency(value: number): string {
  return new Intl.NumberFormat("en-US", { style: "currency", currency: "USD" }).format(value);
}

export function initials(name: string): string {
  return name
    .split(/\s+/)
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase() ?? "")
    .join("");
}

export function isLowStock(stockQuantity: number, lowStockThreshold: number): boolean {
  return stockQuantity <= lowStockThreshold;
}
