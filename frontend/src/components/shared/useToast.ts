import { toast } from "sonner";

type ToastKind = "success" | "error";
type Toast = { kind: ToastKind; title: string; message?: string };

export function useToast() {
  return {
    notify(nextToast: Toast) {
      const showToast = nextToast.kind === "success" ? toast.success : toast.error;
      showToast(nextToast.title, { description: nextToast.message });
    },
  };
}
