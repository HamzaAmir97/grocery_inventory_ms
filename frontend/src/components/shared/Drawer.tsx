import type { ReactNode } from "react";
import {
  Sheet,
  SheetContent,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";

export function Drawer({ open, onClose, title, children, footer }: { open: boolean; onClose: () => void; title: ReactNode; children: ReactNode; footer?: ReactNode }) {
  return (
    <Sheet open={open} onOpenChange={(nextOpen) => !nextOpen && onClose()}>
      <SheetContent side="right" className="drawer">
        <SheetHeader className="drawer-header">
          <SheetTitle className="modal-title">{title}</SheetTitle>
        </SheetHeader>
        <div className="drawer-body">{children}</div>
        {footer ? <SheetFooter className="drawer-footer">{footer}</SheetFooter> : null}
      </SheetContent>
    </Sheet>
  );
}
