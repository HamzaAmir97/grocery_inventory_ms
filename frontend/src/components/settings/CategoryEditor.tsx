"use client";

import { useState } from "react";
import {
  Button,
  FormDialog,
  Input,
  Select,
  Textarea,
  useToast,
} from "@/components/shared";
import {
  useCreateCategoryMutation,
  useUpdateCategoryMutation,
} from "@/hooks/settings";
import { validateCategory } from "@/lib/settings/schemas";
import type { Category } from "@/types";

export function CategoryEditor({ editing, onClose, onSaved }: { editing: Category | null; onClose: () => void; onSaved: () => void }) {
  const { notify } = useToast();
  const createCategoryMutation = useCreateCategoryMutation();
  const updateCategoryMutation = useUpdateCategoryMutation();
  const [name, setName] = useState(editing?.name ?? "");
  const [description, setDescription] = useState(editing?.description ?? "");
  const [isActive, setIsActive] = useState(editing?.is_active ?? true);
  const [errors, setErrors] = useState<{ name?: string }>({});
  const busy = createCategoryMutation.isPending || updateCategoryMutation.isPending;

  async function submit() {
    const result = validateCategory({ name, description, is_active: isActive });
    setErrors(result.errors);
    if (result.errors.name) return;
    try {
      const payload = { name, description, is_active: isActive };
      if (editing) await updateCategoryMutation.mutateAsync({ id: editing.id, payload });
      else await createCategoryMutation.mutateAsync(payload);
      notify({ kind: "success", title: editing ? "Category saved" : "Category created" });
      onSaved();
    } catch (saveError) {
      notify({ kind: "error", title: "Could not save category", message: saveError instanceof Error ? saveError.message : undefined });
    }
  }

  return (
    <FormDialog
      open
      onClose={onClose}
      title={editing ? "Edit category" : "Add category"}
      footer={<><Button variant="secondary" onClick={onClose} disabled={busy}>Cancel</Button><Button onClick={submit} disabled={busy}>{busy ? "Saving…" : "Save"}</Button></>}
    >
      <div style={{ display: "grid", gap: 16 }}>
        <Input label="Name" value={name} error={errors.name} onChange={(event) => setName(event.target.value)} placeholder="e.g. Pantry" />
        <Textarea label="Description" optional value={description ?? ""} onChange={(event) => setDescription(event.target.value)} />
        <Select label="Status" value={isActive ? "active" : "inactive"} onChange={(event) => setIsActive(event.target.value === "active")}>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </Select>
      </div>
    </FormDialog>
  );
}
