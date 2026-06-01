import { formatNumber } from "@/lib/format";
import type { DashboardGrowthPoint, DashboardSummaryCard } from "@/types";
import { DashboardMiniBadge } from "./DashboardMiniBadge";
import { Sparkline } from "./Sparkline";
import { statIcon } from "./helpers";

export function DashboardStatCards({
  cards,
  growth,
}: {
  cards: DashboardSummaryCard[];
  growth: DashboardGrowthPoint[];
}) {
  const sparklinePoints = growth.slice(-6).map((point) => point.count);

  return (
    <div className="dashboard-stat-grid">
      {cards.map((card) => (
        <div key={card.key} className="dashboard-stat-card">
          <div className="dashboard-stat-top">
            <div className="dashboard-stat-label-wrap">
              <span className="dashboard-stat-icon">{statIcon(card.key)}</span>
              <span className="dashboard-stat-label">{card.label}</span>
            </div>
            <DashboardMiniBadge tone={card.badge_tone}>
              {card.badge}
            </DashboardMiniBadge>
          </div>
          <div className="dashboard-stat-bottom">
            <span className="dashboard-stat-value">
              {formatNumber(card.value)}
            </span>
            <Sparkline points={sparklinePoints} />
          </div>
        </div>
      ))}
    </div>
  );
}
