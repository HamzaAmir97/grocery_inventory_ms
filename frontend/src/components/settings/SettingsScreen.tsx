"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import { useSearchParams } from "next/navigation";
import {
  Button,
  ConfirmDialog,
  EmptyState,
  ErrorAlert,
  FormDialog,
  IconEdit,
  IconPlus,
  IconTrash,
  Input,
  PageHeader,
  Pagination,
  Select,
  useToast,
} from "@/components/shared";
import { Button as ShadcnButton } from "@/components/ui/button";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { ApiClientError } from "@/lib/axios-instance";
import type { SettingBase } from "@/types";
import { SettingsSortHeader } from "./SettingsSortHeader";
import { SettingsTableSkeleton } from "./SettingsTableSkeleton";
import type { SettingsScreenProps, SettingsSortField } from "./helpers";

export function SettingsScreen<T extends SettingBase>({
  title,
  description,
  addLabel,
  icon,
  records,
  meta,
  filters,
  onFiltersChange,
  isLoading,
  error = "",
  isDeleting,
  remove,
  columns,
  renderEditor,
  parentFilter,
  matchesParent,
}: SettingsScreenProps<T>) {
  const { notify } = useToast();
  const searchParams = useSearchParams();
  const routeSearch = searchParams.get("search")?.trim() ?? "";
  const lastRouteSearchRef = useRef<string | null>(null);
  const [status, setStatus] = useState<"all" | "active" | "inactive">("all");
  const [editorOpen, setEditorOpen] = useState(false);
  const [editing, setEditing] = useState<T | null>(null);
  const [pendingDelete, setPendingDelete] = useState<T | null>(null);
  const [blocked, setBlocked] = useState<T | null>(null);

  const filtered = useMemo(() => {
    return records.filter((record) => {
      if (status === "active" && !record.is_active) return false;
      if (status === "inactive" && record.is_active) return false;
      if (matchesParent && !matchesParent(record)) return false;
      return true;
    });
  }, [records, status, matchesParent]);

  useEffect(() => {
    if (lastRouteSearchRef.current === routeSearch) return;

    lastRouteSearchRef.current = routeSearch;
    onFiltersChange({ ...filters, search: routeSearch || undefined, page: 1 });
  }, [filters, onFiltersChange, routeSearch]);

  function updateSearch(search: string) {
    onFiltersChange({ ...filters, search: search || undefined, page: 1 });
  }

  function sortBy(field: SettingsSortField) {
    const nextDirection =
      filters.sort_by === field && filters.sort_dir === "asc" ? "desc" : "asc";

    onFiltersChange({ ...filters, sort_by: field, sort_dir: nextDirection, page: 1 });
  }

  async function confirmDelete() {
    if (!pendingDelete) return;
    try {
      await remove(pendingDelete.id);
      notify({ kind: "success", title: `${title.replace(/s$/, "")} deleted` });
      setPendingDelete(null);
    } catch (deleteError) {
      if (deleteError instanceof ApiClientError && deleteError.status === 409) {
        setBlocked(pendingDelete);
        setPendingDelete(null);
      } else {
        notify({ kind: "error", title: "Could not delete", message: deleteError instanceof Error ? deleteError.message : undefined });
      }
    }
  }

  function openCreate() {
    setEditing(null);
    setEditorOpen(true);
  }

  function openEdit(record: T) {
    setEditing(record);
    setEditorOpen(true);
  }

  return (
    <>
      <div style={{ display: "grid", gap: 20 }}>
        <PageHeader
          title={title}
          description={description}
          action={<Button icon={<IconPlus size={16} />} onClick={openCreate}>{addLabel}</Button>}
        />

        <div style={{ display: "flex", gap: 12, flexWrap: "wrap", alignItems: "end" }}>
          <div style={{ flex: "1 1 240px" }}>
            <Input label="Search" placeholder="Search by name" value={filters.search ?? ""} onChange={(event) => updateSearch(event.target.value)} />
          </div>
          {parentFilter}
          <div style={{ width: 180 }}>
            <Select label="Status" value={status} onChange={(event) => setStatus(event.target.value as typeof status)}>
              <option value="all">All statuses</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </Select>
          </div>
        </div>

        {isLoading ? <SettingsTableSkeleton columns={columns} rows={5} /> : null}
        {error ? <ErrorAlert message={error} /> : null}

        {!isLoading && !error ? (
          filtered.length === 0 ? (
            <EmptyState
              icon={icon}
              title={`No ${title.toLowerCase()} yet`}
              message={`Add your first ${title.toLowerCase().replace(/s$/, "")} to get started.`}
              action={<Button icon={<IconPlus size={16} />} onClick={openCreate}>{addLabel}</Button>}
            />
          ) : (
            <div className="table-wrap">
              <div className="table-scroll">
                <Table className="tbl">
                  <TableHeader>
                    <TableRow>
                      {columns.map((column) => (
                        <TableHead key={column.header} className={column.numeric ? "num" : undefined}>
                          {column.sortField ? (
                            <SettingsSortHeader
                              align={column.numeric ? "end" : "start"}
                              field={column.sortField}
                              filters={filters}
                              label={column.header}
                              onSort={sortBy}
                            />
                          ) : (
                            column.header
                          )}
                        </TableHead>
                      ))}
                      <TableHead style={{ textAlign: "right" }}>Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {filtered.map((record) => (
                      <TableRow key={record.id}>
                        {columns.map((column) => (
                          <TableCell key={column.header} className={column.numeric ? "num" : undefined}>{column.render(record)}</TableCell>
                        ))}
                        <TableCell>
                          <div style={{ display: "flex", gap: 6, justifyContent: "flex-end" }}>
                            <ShadcnButton type="button" variant="ghost" size="icon-sm" className="btn-icon" aria-label={`Edit ${record.name}`} onClick={() => openEdit(record)}>
                              <IconEdit size={16} />
                            </ShadcnButton>
                            <ShadcnButton type="button" variant="ghost" size="icon-sm" className="btn-icon" aria-label={`Delete ${record.name}`} onClick={() => setPendingDelete(record)} style={{ color: "var(--color-danger)" }}>
                              <IconTrash size={16} />
                            </ShadcnButton>
                          </div>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </div>
            </div>
          )
        ) : null}

        {meta && meta.total > 0 ? (
          <Pagination
            page={meta.current_page}
            lastPage={meta.last_page}
            total={meta.total}
            perPage={meta.per_page}
            onChange={(page) => onFiltersChange({ ...filters, page })}
            onPerPageChange={(per_page) => onFiltersChange({ ...filters, per_page, page: 1 })}
          />
        ) : null}
      </div>

      {editorOpen ? renderEditor({ editing, onClose: () => setEditorOpen(false), onSaved: () => setEditorOpen(false) }) : null}

      <ConfirmDialog
        open={Boolean(pendingDelete)}
        onClose={() => setPendingDelete(null)}
        title={`Delete ${title.toLowerCase().replace(/s$/, "")}?`}
        message={pendingDelete ? `Delete "${pendingDelete.name}"? This action cannot be undone.` : ""}
        confirmLabel="Delete"
        busy={isDeleting}
        onConfirm={confirmDelete}
      />

      <FormDialog
        open={Boolean(blocked)}
        onClose={() => setBlocked(null)}
        title="Can't delete this record"
        footer={<Button variant="secondary" onClick={() => setBlocked(null)}>Got it</Button>}
      >
        <p className="muted" style={{ fontSize: 14, lineHeight: 1.55 }}>
          {blocked ? `"${blocked.name}" is currently used by inventory items or related records. Reassign or remove those first, then try again.` : ""}
        </p>
      </FormDialog>
    </>
  );
}
