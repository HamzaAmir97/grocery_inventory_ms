import type { ReactNode } from "react";
import { Button } from "./Button";
import { Modal } from "./Modal";

export function ConfirmDialog({ open, onClose, title, message, confirmLabel = "Delete", onConfirm, busy, danger = true }: { open: boolean; onClose: () => void; title: string; message: ReactNode; confirmLabel?: string; onConfirm: () => void; busy?: boolean; danger?: boolean }) {
  return (
    <Modal
      open={open}
      onClose={onClose}
      title={title}
      footer={
        <>
          <Button variant="secondary" onClick={onClose} disabled={busy}>Cancel</Button>
          <Button variant={danger ? "danger" : "primary"} onClick={onConfirm} disabled={busy}>
            {busy ? "Working…" : confirmLabel}
          </Button>
        </>
      }
    >
      <p className="muted" style={{ fontSize: 14, lineHeight: 1.55 }}>{message}</p>
    </Modal>
  );
}
