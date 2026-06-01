import { Button } from "./Button";

export function Pagination({ page, lastPage, total, perPage, onChange }: { page: number; lastPage: number; total: number; perPage: number; onChange: (page: number) => void }) {
  const from = total === 0 ? 0 : (page - 1) * perPage + 1;
  const to = Math.min(page * perPage, total);
  return (
    <div className="row" style={{ justifyContent: "space-between", padding: "12px 4px" }}>
      <span className="muted" style={{ fontSize: 13 }}>
        Showing {from}–{to} of {total}
      </span>
      <div className="row" style={{ gap: 8 }}>
        <Button variant="secondary" size="sm" disabled={page <= 1} onClick={() => onChange(page - 1)}>Previous</Button>
        <span className="muted" style={{ fontSize: 13, fontWeight: 600 }}>Page {page} of {Math.max(lastPage, 1)}</span>
        <Button variant="secondary" size="sm" disabled={page >= lastPage} onClick={() => onChange(page + 1)}>Next</Button>
      </div>
    </div>
  );
}
