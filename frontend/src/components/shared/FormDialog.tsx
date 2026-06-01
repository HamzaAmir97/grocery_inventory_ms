import type { ReactNode } from "react";
import { Modal } from "./Modal";

export function FormDialog({ open, onClose, title, children, footer, size }: { open: boolean; onClose: () => void; title: ReactNode; children: ReactNode; footer?: ReactNode; size?: "lg" }) {
  return (
    <Modal open={open} onClose={onClose} title={title} footer={footer} size={size}>
      {children}
    </Modal>
  );
}
