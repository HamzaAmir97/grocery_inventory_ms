import { Card, EmptyState, IconAlertTriangle } from "@/components/shared";
import { formatNumber } from "@/lib/format";
import type { DashboardItem } from "@/types";
import { DashboardMiniBadge } from "./DashboardMiniBadge";

export function LowStockPanel({ items }: { items: DashboardItem[] }) {
  return (
    <Card className="dashboard-panel dashboard-low-stock-panel">
      <div className="dashboard-panel-head">
        <div>
          <h2>Low stock warning</h2>
        </div>
        <DashboardMiniBadge tone="danger">{`${items.length} ${items.length === 1 ? "item" : "items"}`}</DashboardMiniBadge>
      </div>

      {items.length === 0 ? (
        <EmptyState
          icon={<IconAlertTriangle size={24} />}
          title="All stocked up"
          message="Items below their threshold will appear here."
        />
      ) : (
        <div className="dashboard-low-stock-list">
          {items.map((item) => (
            <div key={item.id} className="dashboard-low-stock-item">
              <div className="dashboard-low-stock-bullet">*</div>
              <div className="dashboard-low-stock-copy">
                <div className="dashboard-low-stock-name">{item.name}</div>
                <div className="dashboard-low-stock-sku">
                  {item.sku ?? "No SKU"}
                </div>
              </div>
              <div className="dashboard-low-stock-qty">
                <div>
                  {formatNumber(item.stock_quantity)} {item.unit_symbol}
                </div>
                <div>
                  of {formatNumber(item.low_stock_threshold)} {item.unit_symbol}
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </Card>
  );
}
