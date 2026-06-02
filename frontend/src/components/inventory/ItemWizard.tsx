"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import { ROUTES } from "@/constants";
import {
  Button,
  Card,
  ErrorAlert,
  Input,
  PageHeader,
  Pill,
  Select,
  Textarea,
  useToast,
} from "@/components/shared";
import {
  useCreateItemMutation,
  useInventoryItemQuery,
  useUpdateItemMutation,
} from "@/hooks/inventory";
import {
  useCategoriesLookupQuery,
  useSubcategoriesLookupQuery,
  useSuppliersLookupQuery,
  useUnitsLookupQuery,
} from "@/hooks/lookups";
import { validateItem } from "@/lib/inventory/schemas";
import { formatCurrency, formatNumber } from "@/lib/format";
import type { ItemFormValues, LookupOption } from "@/types";
import { ItemFormSkeleton } from "./ItemFormSkeleton";
import { Stepper } from "./Stepper";
import { EMPTY_VALUES, STEPS } from "./helpers";

export function ItemWizard({ mode, itemId, title }: { mode: "create" | "edit"; itemId?: string; title: string }) {
  const router = useRouter();
  const { notify } = useToast();
  const [step, setStep] = useState(0);
  const [values, setValues] = useState<ItemFormValues>(EMPTY_VALUES);
  const [errors, setErrors] = useState<Partial<Record<keyof ItemFormValues, string>>>({});
  const itemQuery = useInventoryItemQuery(mode === "edit" && itemId ? itemId : "");
  const createItemMutation = useCreateItemMutation();
  const updateItemMutation = useUpdateItemMutation();
  const categoriesQuery = useCategoriesLookupQuery();
  const subcategoriesQuery = useSubcategoriesLookupQuery(values.category_id || undefined);
  const unitsQuery = useUnitsLookupQuery();
  const suppliersQuery = useSuppliersLookupQuery();
  const categories = categoriesQuery.data?.data ?? [];
  const subcategories = subcategoriesQuery.data?.data ?? [];
  const units = unitsQuery.data?.data ?? [];
  const suppliers = suppliersQuery.data?.data ?? [];
  const loadError = itemQuery.error instanceof Error ? itemQuery.error.message : "";
  const submitting = createItemMutation.isPending || updateItemMutation.isPending;
  const loadingItem = mode === "edit" && itemQuery.isLoading;

  useEffect(() => {
    if (mode !== "edit" || !itemQuery.data?.data) return;

    const item = itemQuery.data.data;
    const timeoutId = window.setTimeout(() => {
      setValues({
        name: item.name,
        sku: item.sku,
        category_id: item.category_id,
        subcategory_id: item.subcategory_id,
        unit_id: item.unit_id,
        supplier_id: item.supplier_id,
        price: item.price,
        stock_quantity: item.stock_quantity,
        low_stock_threshold: item.low_stock_threshold,
        description: item.description ?? "",
        is_active: item.is_active,
      });
    }, 0);

    return () => window.clearTimeout(timeoutId);
  }, [mode, itemQuery.data]);

  function set<K extends keyof ItemFormValues>(key: K, value: ItemFormValues[K]) {
    setValues((current) => ({ ...current, [key]: value }));
  }

  function validateStep(currentStep: number): boolean {
    const result = validateItem(values);
    const stepFields: Record<number, (keyof ItemFormValues)[]> = {
      0: ["name", "sku"],
      1: [],
      2: ["price", "stock_quantity", "low_stock_threshold"],
      3: [],
    };
    const localErrors: Partial<Record<keyof ItemFormValues, string>> = {};
    stepFields[currentStep].forEach((field) => {
      if (result.errors[field]) localErrors[field] = result.errors[field];
    });
    if (currentStep === 1) {
      if (!values.category_id) localErrors.category_id = "Category is required.";
      if (!values.subcategory_id) localErrors.subcategory_id = "Subcategory is required.";
      if (!values.unit_id) localErrors.unit_id = "Unit is required.";
      if (!values.supplier_id) localErrors.supplier_id = "Supplier is required.";
    }
    setErrors(localErrors);
    return Object.keys(localErrors).length === 0;
  }

  function next() {
    if (validateStep(step)) setStep((value) => Math.min(value + 1, STEPS.length - 1));
  }

  async function save() {
    try {
      if (mode === "edit" && itemId) {
        await updateItemMutation.mutateAsync({ id: itemId, payload: values });
        notify({ kind: "success", title: "Item saved" });
      } else {
        await createItemMutation.mutateAsync(values);
        notify({ kind: "success", title: "Item created" });
      }
      router.push(ROUTES.inventory);
    } catch (saveError) {
      notify({ kind: "error", title: "Could not save item", message: saveError instanceof Error ? saveError.message : undefined });
    }
  }

  const lowStockPreview = values.stock_quantity <= values.low_stock_threshold && values.low_stock_threshold > 0;

  const lookupName = (options: LookupOption[], id: number) => options.find((option) => option.id === id)?.name ?? "—";

  return (
    <div style={{ display: "grid", gap: 20, maxWidth: 760, margin: "0 auto", width: "100%" }}>
        <PageHeader title={title} description="Dropdown values are managed from Settings." />

        {loadError ? <ErrorAlert message={loadError} /> : null}

        <Card padded={false}>
          <div style={{ padding: "20px 24px", borderBottom: "1px solid var(--color-border)" }}>
            <Stepper step={step} errors={errors} />
          </div>

          <div style={{ padding: 24 }} aria-busy={loadingItem}>
            {loadingItem ? (
              <ItemFormSkeleton />
            ) : (
              <>
            {step === 0 ? (
              <div className="form-grid">
                <div className="span-2">
                  <Input label="Item name" value={values.name} error={errors.name} onChange={(event) => set("name", event.target.value)} placeholder="e.g. Basmati rice" />
                </div>
                <Input label="SKU" value={values.sku} error={errors.sku} onChange={(event) => set("sku", event.target.value)} placeholder="e.g. GR-RICE-001" />
                <Select label="Status" value={values.is_active ? "active" : "inactive"} onChange={(event) => set("is_active", event.target.value === "active")}>
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </Select>
                <div className="span-2">
                  <Textarea label="Description" optional value={values.description ?? ""} onChange={(event) => set("description", event.target.value)} placeholder="Short description of the item" />
                </div>
              </div>
            ) : null}

            {step === 1 ? (
              <div className="form-grid">
                <Select
                  label="Category"
                  value={values.category_id || ""}
                  error={errors.category_id}
                  onChange={(event) => {
                    set("category_id", Number(event.target.value));
                    set("subcategory_id", 0);
                  }}
                >
                  <option value="">Select category</option>
                  {categories.map((option) => <option key={option.id} value={option.id}>{option.name}</option>)}
                </Select>
                <Select label="Subcategory" value={values.subcategory_id || ""} error={errors.subcategory_id} disabled={!values.category_id} onChange={(event) => set("subcategory_id", Number(event.target.value))}>
                  <option value="">{values.category_id ? "Select subcategory" : "Select a category first"}</option>
                  {subcategories.map((option) => <option key={option.id} value={option.id}>{option.name}</option>)}
                </Select>
                <Select label="Unit" value={values.unit_id || ""} error={errors.unit_id} onChange={(event) => set("unit_id", Number(event.target.value))}>
                  <option value="">Select unit</option>
                  {units.map((option) => <option key={option.id} value={option.id}>{option.name}</option>)}
                </Select>
                <Select label="Supplier" value={values.supplier_id || ""} error={errors.supplier_id} onChange={(event) => set("supplier_id", Number(event.target.value))}>
                  <option value="">Select supplier</option>
                  {suppliers.map((option) => <option key={option.id} value={option.id}>{option.name}</option>)}
                </Select>
                <p className="span-2 field-helper" style={{ margin: 0 }}>
                  Categories, units, and suppliers are loaded from your database. Manage them under Settings → Database.
                </p>
              </div>
            ) : null}

            {step === 2 ? (
              <div className="form-grid">
                <Input label="Price" type="number" min={0} step="0.01" value={values.price} error={errors.price} onChange={(event) => set("price", Number(event.target.value))} />
                <Input label="Stock quantity" type="number" min={0} value={values.stock_quantity} error={errors.stock_quantity} onChange={(event) => set("stock_quantity", Number(event.target.value))} />
                <Input label="Low stock threshold" type="number" min={0} value={values.low_stock_threshold} error={errors.low_stock_threshold} onChange={(event) => set("low_stock_threshold", Number(event.target.value))} />
                {lowStockPreview ? (
                  <div className="span-2" style={{ display: "flex", alignItems: "center", gap: 10, padding: "10px 14px", borderRadius: "var(--radius-lg)", background: "var(--color-warning-soft)", color: "var(--color-warning)", fontSize: 13, fontWeight: 600 }}>
                    <Pill tone="warning">Low stock</Pill>
                    This item would be flagged as low stock at the current quantity.
                  </div>
                ) : null}
              </div>
            ) : null}

            {step === 3 ? (
              <div style={{ display: "grid", gap: 16 }}>
                <dl className="review-grid">
                  <dt>Item name</dt><dd>{values.name || "—"}</dd>
                  <dt>SKU</dt><dd className="mono">{values.sku || "—"}</dd>
                  <dt>Status</dt><dd>{values.is_active ? "Active" : "Inactive"}</dd>
                  <dt>Category</dt><dd>{lookupName(categories, values.category_id)}</dd>
                  <dt>Subcategory</dt><dd>{lookupName(subcategories, values.subcategory_id)}</dd>
                  <dt>Unit</dt><dd>{lookupName(units, values.unit_id)}</dd>
                  <dt>Supplier</dt><dd>{lookupName(suppliers, values.supplier_id)}</dd>
                  <dt>Price</dt><dd>{formatCurrency(values.price)}</dd>
                  <dt>Stock</dt><dd>{formatNumber(values.stock_quantity)}</dd>
                  <dt>Low stock at</dt><dd>{formatNumber(values.low_stock_threshold)}</dd>
                </dl>
                {values.description ? <p className="muted" style={{ fontSize: 13 }}>{values.description}</p> : null}
              </div>
            ) : null}
              </>
            )}
          </div>

          <div style={{ display: "flex", justifyContent: "space-between", gap: 8, padding: "16px 24px", borderTop: "1px solid var(--color-border)" }}>
            <Link href={ROUTES.inventory}>
              <Button variant="ghost">Cancel</Button>
            </Link>
            <div style={{ display: "flex", gap: 8 }}>
              {step > 0 ? <Button variant="secondary" onClick={() => setStep((value) => value - 1)} disabled={loadingItem}>Back</Button> : null}
              {step < STEPS.length - 1 ? (
                <Button onClick={next} disabled={loadingItem}>Next</Button>
              ) : (
                <Button onClick={save} disabled={submitting}>{submitting ? "Saving…" : "Save item"}</Button>
              )}
            </div>
          </div>
        </Card>
    </div>
  );
}
