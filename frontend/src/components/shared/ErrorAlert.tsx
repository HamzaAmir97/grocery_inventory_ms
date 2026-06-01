import {
  Alert as ShadcnAlert,
  AlertDescription as ShadcnAlertDescription,
} from "@/components/ui/alert";
import { IconAlert } from "./icons";

export function ErrorAlert({ message }: { message: string }) {
  return (
    <ShadcnAlert
      variant="destructive"
      className="error-alert"
      style={{
        display: "flex",
        alignItems: "flex-start",
        gap: 10,
        borderRadius: "var(--radius-lg)",
        border: "1px solid var(--color-danger)",
        background: "var(--color-danger-soft)",
        color: "var(--color-danger)",
        padding: "12px 14px",
        fontSize: 13,
        fontWeight: 600,
      }}
    >
      <IconAlert size={16} />
      <ShadcnAlertDescription>{message}</ShadcnAlertDescription>
    </ShadcnAlert>
  );
}
