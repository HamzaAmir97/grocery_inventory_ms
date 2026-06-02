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
  useCreateSubcategoryMutation,
  useUpdateSubcategoryMutation,
} from "@/hooks/settings";
import { validateSubcategory } from "@/lib/settings/schemas";
import type { LookupOption, Subcategory } from "@/types";

export function SubcategoryEditor({ editing, categories, onClose, onSaved }: { editing: Subcategory | null; categories: LookupOption[]; onClose: () => void; onSaved: () => void }) {
  const { notify } = useToast();
  const createSubcategoryMutation = useCreateSubcategoryMutation();
  const updateSubcategoryMutation = useUpdateSubcategoryMutation();
  const [name, setName] = useState(editing?.name ?? "");
  const [categoryId, setCategoryId] = useState(editing?.category_id ?? 0);
  const [description, setDescription] = useState(editing?.description ?? "");
  const [isActive, setIsActive] = useState(editing?.is_active ?? true);
  const [errors, setErrors] = useState<{ name?: string; category_id?: string }>({});
  const busy = createSubcategoryMutation.isPending || updateSubcategoryMutation.isPending;

  async function submit() {
    const result = validateSubcategory({ name, category_id: categoryId, description, is_active: isActive });
    setErrors(result.errors);
    if (result.errors.name || result.errors.category_id) return;
    try {
      const payload = { name, category_id: categoryId, description, is_active: isActive };
      if (editing) await updateSubcategoryMutation.mutateAsync({ id: editing.id, payload });
      else await createSubcategoryMutation.mutateAsync(payload);
      notify({ kind: "success", title: editing ? "Subcategory saved" : "Subcategory created" });
      onSaved();
    } catch (saveError) {
      notify({ kind: "error", title: "Could not save subcategory", message: saveError instanceof Error ? saveError.message : undefined });
    }
  }

  return (
    <FormDialog
      open
      onClose={onClose}
      title={editing ? "Edit subcategory" : "Add subcategory"}
      footer={<><Button variant="secondary" onClick={onClose} disabled={busy}>Cancel</Button><Button onClick={submit} disabled={busy}>{busy ? "Saving…" : "Save"}</Button></>}
    >
      <div style={{ display: "grid", gap: 16 }}>
        <Select label="Parent category" value={categoryId || ""} error={errors.category_id} onChange={(event) => setCategoryId(Number(event.target.value))}>
          <option value="">Select category</option>
          {categories.map((option) => <option key={option.id} value={option.id}>{option.name}</option>)}
        </Select>
        <Input label="Name" value={name} error={errors.name} onChange={(event) => setName(event.target.value)} placeholder="e.g. Rice & grains" />
        <Textarea label="Description" optional value={description ?? ""} onChange={(event) => setDescription(event.target.value)} />
        <Select label="Status" value={isActive ? "active" : "inactive"} onChange={(event) => setIsActive(event.target.value === "active")}>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </Select>
      </div>
    </FormDialog>
  );
}
