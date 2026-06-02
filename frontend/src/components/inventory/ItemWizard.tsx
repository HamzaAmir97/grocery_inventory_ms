"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";
import { type ReactNode, useEffect, useState } from "react";
import { ROUTES } from "@/constants";
import {
  Button,
  Card,
  ErrorAlert,
  IconAlert,
  IconBox,
  IconCategory,
  IconCheck,
  IconChevronLeft,
  IconDollar,
  IconEdit,
  Input,
  Pill,
  Select,
  StatusBadge,
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
import { EMPTY_VALUES, STEP_META, STEPS } from "./helpers";

type ReviewRow = { label: string; value: ReactNode };

function ReviewSection({ icon, title, onEdit, rows, children }: { icon: ReactNode; title: string; onEdit: () => void; rows: ReviewRow[]; children?: ReactNode }) {
  return (
    <section className="review-section-card">
      <div className="review-section-header">
        <div className="review-section-title">
          <span className="review-section-icon">{icon}</span>
          <span>{title}</span>
        </div>
        <button type="button" className="review-edit-button" onClick={onEdit}>
          <IconEdit size={10} />
          Edit
        </button>
      </div>
      <div className="review-section-body">
        {rows.map((row) => (
          <div className="review-row" key={row.label}>
            <span className="review-label">{row.label}</span>
            <span className="review-value">{row.value}</span>
          </div>
        ))}
        {children}
      </div>
    </section>
  );
}

function WizardProgressDots({ step }: { step: number }) {
  return (
    <div className="wizard-progress-dots" aria-label={`Step ${step + 1} of ${STEPS.length}`}>
      {STEPS.map((item, index) => (
        <span key={item} className={`wizard-progress-dot ${index === step ? "active" : ""}`} aria-hidden="true" />
      ))}
    </div>
  );
}

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
  const currentStep = STEP_META[step];
  const saveLabel = mode === "edit" ? "Save Changes" : "Save Item";
  const stockProgress = values.low_stock_threshold > 0
    ? Math.min((values.stock_quantity / values.low_stock_threshold) * 100, 100)
    : 100;
  const stockSummary = values.low_stock_threshold > 0
    ? `${formatNumber(values.stock_quantity)} / ${formatNumber(values.low_stock_threshold)} units`
    : `${formatNumber(values.stock_quantity)} units`;
  const basicRows: ReviewRow[] = [
    { label: "Item Name", value: values.name || "—" },
    { label: "SKU", value: <span className="mono">{values.sku || "—"}</span> },
    { label: "Status", value: <StatusBadge active={values.is_active} /> },
    ...(values.description ? [{ label: "Description", value: values.description }] : []),
  ];
  const classificationRows: ReviewRow[] = [
    { label: "Category", value: lookupName(categories, values.category_id) },
    { label: "Subcategory", value: lookupName(subcategories, values.subcategory_id) },
    { label: "Unit", value: lookupName(units, values.unit_id) },
    { label: "Supplier", value: lookupName(suppliers, values.supplier_id) },
  ];
  const pricingRows: ReviewRow[] = [
    { label: "Unit Price", value: formatCurrency(values.price) },
    { label: "Stock Quantity", value: `${formatNumber(values.stock_quantity)} units` },
    { label: "Low Threshold", value: `${formatNumber(values.low_stock_threshold)} units` },
  ];

  return (
    <div className="item-wizard" aria-label={title}>
      {loadError ? <ErrorAlert message={loadError} /> : null}

      <Card className="wizard-stepper-card" padded={false}>
        <Stepper step={step} errors={errors} />
      </Card>

      <Card className="item-wizard-card" padded={false}>
        <div className="wizard-card-header">
          <div className="wizard-card-title-group">
            <span className="wizard-current-step-badge">{step + 1}</span>
            <div>
              <h1 className="wizard-card-title">{currentStep.title}</h1>
              <p className="wizard-card-subtitle">{currentStep.subtitle}</p>
            </div>
          </div>
          <span className="wizard-step-count">Step {step + 1} of {STEPS.length}</span>
        </div>

        <div className="wizard-card-body" aria-busy={loadingItem}>
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
                    Categories, units, and suppliers are loaded from your database. Manage them under Settings &gt; Database.
                  </p>
                </div>
              ) : null}

              {step === 2 ? (
                <div className="form-grid">
                  <Input label="Price" type="number" min={0} step="0.01" value={values.price} error={errors.price} onChange={(event) => set("price", Number(event.target.value))} />
                  <Input label="Stock quantity" type="number" min={0} value={values.stock_quantity} error={errors.stock_quantity} onChange={(event) => set("stock_quantity", Number(event.target.value))} />
                  <Input label="Low stock threshold" type="number" min={0} value={values.low_stock_threshold} error={errors.low_stock_threshold} onChange={(event) => set("low_stock_threshold", Number(event.target.value))} />
                  {lowStockPreview ? (
                    <div className="wizard-low-stock-preview span-2">
                      <Pill tone="warning">Low stock</Pill>
                      This item would be flagged as low stock at the current quantity.
                    </div>
                  ) : null}
                </div>
              ) : null}

              {step === 3 ? (
                <div className="review-confirm">
                  <div className="review-alert">
                    <span className="review-alert-icon"><IconAlert size={12} /></span>
                    <div>
                      <p className="review-alert-title">Review your changes</p>
                      <p className="review-alert-copy">Click Edit on any section to update, then save.</p>
                    </div>
                  </div>

                  <ReviewSection icon={<IconBox size={12} />} title="Basic Information" rows={basicRows} onEdit={() => setStep(0)} />
                  <ReviewSection icon={<IconCategory size={12} />} title="Classification" rows={classificationRows} onEdit={() => setStep(1)} />
                  <ReviewSection icon={<IconDollar size={12} />} title="Pricing & Stock" rows={pricingRows} onEdit={() => setStep(2)}>
                    <div className={`review-stock-meter ${lowStockPreview ? "warning" : "healthy"}`}>
                      <div className="review-stock-meter-head">
                        <span className="review-stock-state">
                          {lowStockPreview ? <IconAlert size={10} /> : <IconCheck size={10} />}
                          {lowStockPreview ? "Low Stock" : "Healthy Stock"}
                        </span>
                        <span>{stockSummary}</span>
                      </div>
                      <div className="review-stock-track">
                        <span style={{ width: `${stockProgress}%` }} />
                      </div>
                    </div>
                  </ReviewSection>
                </div>
              ) : null}
            </>
          )}
        </div>

        <div className="wizard-footer">
          <div className="wizard-footer-start">
            {step > 0 ? (
              <Button variant="secondary" size="sm" icon={<IconChevronLeft size={12} />} onClick={() => setStep((value) => value - 1)} disabled={loadingItem}>Back</Button>
            ) : (
              <Link href={ROUTES.inventory}>
                <Button variant="secondary" size="sm" icon={<IconChevronLeft size={12} />}>Back</Button>
              </Link>
            )}
          </div>
          <WizardProgressDots step={step} />
          <div className="wizard-footer-end">
            {step < STEPS.length - 1 ? (
              <Button size="sm" onClick={next} disabled={loadingItem}>Next</Button>
            ) : (
              <Button className="wizard-save-button" size="sm" icon={submitting ? undefined : <IconCheck size={12} />} onClick={save} disabled={submitting}>{submitting ? "Saving..." : saveLabel}</Button>
            )}
          </div>
        </div>
      </Card>
    </div>
  );
}
