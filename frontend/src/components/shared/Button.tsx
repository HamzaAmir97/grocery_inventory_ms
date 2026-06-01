import type { ButtonHTMLAttributes, ReactNode } from "react";
import { Button as ShadcnButton } from "@/components/ui/button";
import { cn } from "@/lib/utils";

type ButtonVariant = "primary" | "secondary" | "ghost" | "danger";

type ButtonProps = ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: ButtonVariant;
  size?: "sm" | "md";
  icon?: ReactNode;
  block?: boolean;
};

export function Button({ variant = "primary", size = "md", icon, block, className = "", children, type = "button", ...props }: ButtonProps) {
  const mappedVariant =
    variant === "primary"
      ? "default"
      : variant === "danger"
        ? "destructive"
        : variant;
  const mappedSize = size === "sm" ? "sm" : "default";

  return (
    <ShadcnButton
      type={type}
      variant={mappedVariant}
      size={mappedSize}
      className={cn(
        "btn",
        `btn-${variant}`,
        size === "sm" && "btn-sm",
        block && "btn-block",
        className
      )}
      {...props}
    >
      {icon}
      {children}
    </ShadcnButton>
  );
}
