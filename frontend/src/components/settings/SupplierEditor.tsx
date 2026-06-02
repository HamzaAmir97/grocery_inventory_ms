"use client";

import { useState } from "react";
import {
  Button,
  Drawer,
  Input,
  Select,
  Textarea,
  useToast,
} from "@/components/shared";
import {
  useCreateSupplierMutation,
  useUpdateSupplierMutation,
} from "@/hooks/settings";
import { validateSupplier } from "@/lib/settings/schemas";
import type { Supplier } from "@/types";

export function SupplierEditor({ editing, onClose, onSaved }: { editing: Supplier | null; onClose: () => void; onSaved: () => void }) {
  const { notify } = useToast();
  const createSupplierMutation = useCreateSupplierMutation();
  const updateSupplierMutation = useUpdateSupplierMutation();
  const [name, setName] = useState(editing?.name ?? "");
  const [contactPerson, setContactPerson] = useState(editing?.contact_person ?? "");
  const [phone, setPhone] = useState(editing?.phone ?? "");
  const [email, setEmail] = useState(editing?.email ?? "");
  const [address, setAddress] = useState(editing?.address ?? "");
  const [isActive, setIsActive] = useState(editing?.is_active ?? true);
  const [errors, setErrors] = useState<{ name?: string; email?: string }>({});
  const busy = createSupplierMutation.isPending || updateSupplierMutation.isPending;

  async function submit() {
    const result = validateSupplier({ name, email, phone, description: null, is_active: isActive });
    setErrors(result.errors);
    if (result.errors.name || result.errors.email) return;
    try {
      const payload = { name, contact_person: contactPerson, phone, email, address, is_active: isActive };
      if (editing) await updateSupplierMutation.mutateAsync({ id: editing.id, payload });
      else await createSupplierMutation.mutateAsync(payload);
      notify({ kind: "success", title: editing ? "Supplier saved" : "Supplier created" });
      onSaved();
    } catch (saveError) {
      notify({ kind: "error", title: "Could not save supplier", message: saveError instanceof Error ? saveError.message : undefined });
    }
  }

  return (
    <Drawer
      open
      onClose={onClose}
      title={editing ? "Edit supplier" : "Add supplier"}
      footer={<><Button variant="secondary" onClick={onClose} disabled={busy}>Cancel</Button><Button onClick={submit} disabled={busy}>{busy ? "Saving…" : "Save supplier"}</Button></>}
    >
      <div style={{ display: "grid", gap: 16 }}>
        <Input label="Supplier name" value={name} error={errors.name} onChange={(event) => setName(event.target.value)} placeholder="e.g. Fresh Farms Co" />
        <Input label="Contact person" optional value={contactPerson ?? ""} onChange={(event) => setContactPerson(event.target.value)} />
        <Input label="Phone" optional value={phone ?? ""} onChange={(event) => setPhone(event.target.value)} />
        <Input label="Email" type="email" optional value={email ?? ""} error={errors.email} onChange={(event) => setEmail(event.target.value)} />
        <Textarea label="Address" optional value={address ?? ""} onChange={(event) => setAddress(event.target.value)} />
        <Select label="Status" value={isActive ? "active" : "inactive"} onChange={(event) => setIsActive(event.target.value === "active")}>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </Select>
      </div>
    </Drawer>
  );
}
