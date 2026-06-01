import type { ButtonHTMLAttributes } from "react";
import { Button as ShadcnButton } from "@/components/ui/button";
import { Tooltip, TooltipContent, TooltipTrigger } from "@/components/ui/tooltip";
import { cn } from "@/lib/utils";

export function IconButton({ children, "aria-label": ariaLabel, className = "", ...props }: ButtonHTMLAttributes<HTMLButtonElement>) {
  return (
    <Tooltip>
      <TooltipTrigger asChild>
        <ShadcnButton
          type="button"
          variant="ghost"
          size="icon-sm"
          className={cn("btn-icon", className)}
          aria-label={ariaLabel}
          {...props}
        >
          {children}
        </ShadcnButton>
      </TooltipTrigger>
      {ariaLabel ? <TooltipContent>{ariaLabel}</TooltipContent> : null}
    </Tooltip>
  );
}
