"use client";

import Link from "next/link";
import { ROUTES } from "@/constants";
import {
  Avatar,
  IconBell,
  IconChevronDown,
  IconPlus,
} from "@/components/shared";
import { SidebarTrigger } from "@/components/ui/sidebar";
import { Button as ShadcnButton } from "@/components/ui/button";
import type { AuthUser } from "@/types";
import { GlobalSearch } from "./GlobalSearch";
import { userInitials, userShortName } from "./helpers";

export function Topbar({
  title,
  subtitle,
  user,
  className = "",
}: {
  title: string;
  subtitle?: string;
  user?: AuthUser | null;
  className?: string;
}) {
  return (
    <header className={`topbar ${className}`.trim()}>
      <SidebarTrigger className="topbar-sidebar-trigger" />
      <div className="topbar-copy">
        <div className="topbar-title">{title}</div>
        {subtitle ? <div className="topbar-subtitle">{subtitle}</div> : null}
      </div>

      <div className="topbar-actions">
        <GlobalSearch />
        <ShadcnButton type="button" variant="ghost" size="icon-sm" className="topbar-bell" aria-label="Notifications">
          <IconBell size={16} />
          <span />
        </ShadcnButton>
        <ShadcnButton asChild className="topbar-add">
          <Link href={ROUTES.inventoryNew}>
            <IconPlus size={16} />
            Add item
          </Link>
        </ShadcnButton>

        <ShadcnButton type="button" variant="outline" className="topbar-user-pill" aria-label={user?.name ?? "Admin user"}>
          <Avatar initials={userInitials(user)} size={28} />
          <span>{userShortName(user)}</span>
          <IconChevronDown size={14} />
        </ShadcnButton>
      </div>
    </header>
  );
}
