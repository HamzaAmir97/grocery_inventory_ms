import { Button } from "./Button";

const DEFAULT_PAGE_SIZE_OPTIONS = [5, 10, 20, 100];

export function Pagination({
  page,
  lastPage,
  total,
  perPage,
  onChange,
  onPerPageChange,
  pageSizeOptions = DEFAULT_PAGE_SIZE_OPTIONS,
}: {
  page: number;
  lastPage: number;
  total: number;
  perPage: number;
  onChange: (page: number) => void;
  onPerPageChange?: (perPage: number) => void;
  pageSizeOptions?: number[];
}) {
  const from = total === 0 ? 0 : (page - 1) * perPage + 1;
  const to = Math.min(page * perPage, total);

  // Keep the active size selectable even if it isn't one of the presets.
  const sizeOptions = pageSizeOptions.includes(perPage)
    ? pageSizeOptions
    : [...pageSizeOptions, perPage].sort((a, b) => a - b);

  return (
    <div className="pagination-bar">
      <div className="pagination-meta">
        {onPerPageChange ? (
          <label className="pagination-size">
            <span>Rows per page</span>
            <select
              className="pagination-size-select"
              value={perPage}
              aria-label="Rows per page"
              onChange={(event) => onPerPageChange(Number(event.target.value))}
            >
              {sizeOptions.map((size) => (
                <option key={size} value={size}>{size}</option>
              ))}
            </select>
          </label>
        ) : null}
        <span className="muted" style={{ fontSize: 13 }}>
          Showing {from}–{to} of {total}
        </span>
      </div>
      <div className="row" style={{ gap: 8 }}>
        <Button variant="secondary" size="sm" disabled={page <= 1} onClick={() => onChange(page - 1)}>Previous</Button>
        <span className="muted" style={{ fontSize: 13, fontWeight: 600 }}>Page {page} of {Math.max(lastPage, 1)}</span>
        <Button variant="secondary" size="sm" disabled={page >= lastPage} onClick={() => onChange(page + 1)}>Next</Button>
      </div>
    </div>
  );
}
