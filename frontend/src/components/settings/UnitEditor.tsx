"use client";

import { useState } from "react";
import {
  Button,
  FormDialog,
  Input,
  Select,
  useToast,
} from "@/components/shared";
import {
  useCreateUnitMutation,
  useUpdateUnitMutation,
} from "@/hooks/settings";
import { validateUnit } from "@/lib/settings/schemas";
import type { Unit } from "@/types";

export function UnitEditor({ editing, onClose, onSaved }: { editing: Unit | null; onClose: () => void; onSaved: () => void }) {
  const { notify } = useToast();
  const createUnitMutation = useCreateUnitMutation();
  const updateUnitMutation = useUpdateUnitMutation();
  const [name, setName] = useState(editing?.name ?? "");
  const [symbol, setSymbol] = useState(editing?.symbol ?? "");
  const [isActive, setIsActive] = useState(editing?.is_active ?? true);
  const [errors, setErrors] = useState<{ name?: string }>({});
  const busy = createUnitMutation.isPending || updateUnitMutation.isPending;

  async function submit() {
    const result = validateUnit({ name, symbol, description: null, is_active: isActive });
    setErrors(result.errors);
    if (result.errors.name) return;
    try {
      const payload = { name, symbol, is_active: isActive };
      if (editing) await updateUnitMutation.mutateAsync({ id: editing.id, payload });
      else await createUnitMutation.mutateAsync(payload);
      notify({ kind: "success", title: editing ? "Unit saved" : "Unit created" });
      onSaved();
    } catch (saveError) {
      notify({ kind: "error", title: "Could not save unit", message: saveError instanceof Error ? saveError.message : undefined });
    }
  }

  return (
    <FormDialog
      open
      onClose={onClose}
      title={editing ? "Edit unit" : "Add unit"}
      footer={<><Button variant="secondary" onClick={onClose} disabled={busy}>Cancel</Button><Button onClick={submit} disabled={busy}>{busy ? "Saving…" : "Save"}</Button></>}
    >
      <div style={{ display: "grid", gap: 16 }}>
        <Input label="Name" value={name} error={errors.name} onChange={(event) => setName(event.target.value)} placeholder="e.g. Kilogram" />
        <Input label="Symbol" value={symbol ?? ""} onChange={(event) => setSymbol(event.target.value)} maxLength={6} placeholder="e.g. kg" />
        <Select label="Status" value={isActive ? "active" : "inactive"} onChange={(event) => setIsActive(event.target.value === "active")}>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </Select>
      </div>
    </FormDialog>
  );
}
