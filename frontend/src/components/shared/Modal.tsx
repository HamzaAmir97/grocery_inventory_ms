import type { ReactNode } from "react";
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { cn } from "@/lib/utils";

export function Modal({ open, onClose, title, children, footer, size }: { open: boolean; onClose: () => void; title: ReactNode; children: ReactNode; footer?: ReactNode; size?: "lg" }) {
  return (
    <Dialog open={open} onOpenChange={(nextOpen) => !nextOpen && onClose()}>
      <DialogContent
        className={cn("modal", size === "lg" && "modal-lg")}
      >
        <DialogHeader className="modal-header">
          <DialogTitle className="modal-title">{title}</DialogTitle>
        </DialogHeader>
        <div>{children}</div>
        {footer ? <DialogFooter className="modal-actions">{footer}</DialogFooter> : null}
      </DialogContent>
    </Dialog>
  );
}
