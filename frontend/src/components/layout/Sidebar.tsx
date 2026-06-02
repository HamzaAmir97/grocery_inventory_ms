"use client";

import Image from "next/image";
import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useState } from "react";
import { NAV_SECTIONS, ROUTES } from "@/constants";
import {
  Avatar,
  IconChevronDown,
  IconDatabase,
  IconLogOut,
  IconSparkles,
} from "@/components/shared";
import {
  Sidebar as ShadcnSidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
  SidebarRail,
} from "@/components/ui/sidebar";
import { Button as ShadcnButton } from "@/components/ui/button";
import { useLogoutMutation } from "@/hooks/auth";
import type { AuthUser } from "@/types";
import { ICONS, pathMatches, userInitials } from "./helpers";

export function Sidebar({ user }: { user?: AuthUser | null }) {
  const pathname = usePathname();
  const router = useRouter();
  const logoutMutation = useLogoutMutation();
  const workspaceSection = NAV_SECTIONS[0];
  const databaseSection = NAV_SECTIONS[1];
  const databaseOpen = pathname.startsWith("/settings");
  const [databaseExpanded, setDatabaseExpanded] = useState(true);
  const showDatabaseItems = databaseExpanded || databaseOpen;

  async function handleLogout() {
    await logoutMutation.mutateAsync().catch(() => undefined);
    router.replace(ROUTES.login);
  }

  return (
    <ShadcnSidebar collapsible="icon" className="inventory-app-sidebar">
      <SidebarHeader className="inventory-sidebar-header">
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton asChild size="lg" className="inventory-sidebar-brand">
              <Link href={ROUTES.dashboard}>
                <span className="sidebar-brand-mark">
                  <Image src="/assets/logo-mark.svg" width={18} height={18} alt="" />
                </span>
                <span className="sidebar-brand-text">Inventory</span>
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>

      <SidebarContent className="inventory-sidebar-content">
        <SidebarGroup>
          <SidebarGroupLabel className="inventory-sidebar-label">{workspaceSection.label}</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
          {workspaceSection.items.map((item) => {
            const Icon = ICONS[item.icon];
            const active = pathMatches(pathname, item.href);

            return (
              <SidebarMenuItem key={item.href}>
                <SidebarMenuButton asChild isActive={active} tooltip={item.label} className="inventory-sidebar-link">
                  <Link href={item.href} aria-current={active ? "page" : undefined}>
                    <Icon size={16} />
                    <span>{item.label}</span>
                  </Link>
                </SidebarMenuButton>
              </SidebarMenuItem>
            );
          })}
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>

        <SidebarGroup>
          <SidebarGroupLabel className="inventory-sidebar-label">Settings</SidebarGroupLabel>
          <SidebarGroupContent>
            <SidebarMenu>
              <SidebarMenuItem>
                <SidebarMenuButton
                  type="button"
                  isActive={databaseOpen}
                  tooltip={databaseSection.label}
                  className="inventory-sidebar-link inventory-sidebar-database"
                  aria-expanded={showDatabaseItems}
                  onClick={() => setDatabaseExpanded((value) => !value)}
                >
                  <IconDatabase size={16} />
                  <span>{databaseSection.label}</span>
                  <IconChevronDown size={14} className="database-chevron" />
                </SidebarMenuButton>
                {showDatabaseItems ? (
                  <SidebarMenuSub className="inventory-sidebar-subnav">
            {databaseSection.items.map((item) => {
              const Icon = ICONS[item.icon];
              const active = pathMatches(pathname, item.href);

              return (
                <SidebarMenuSubItem key={item.href}>
                  <SidebarMenuSubButton asChild isActive={active} className="inventory-sidebar-sublink">
                    <Link href={item.href} aria-current={active ? "page" : undefined}>
                      <Icon size={14} />
                      <span>{item.label}</span>
                    </Link>
                  </SidebarMenuSubButton>
                </SidebarMenuSubItem>
              );
            })}
                  </SidebarMenuSub>
                ) : null}
              </SidebarMenuItem>
            </SidebarMenu>
          </SidebarGroupContent>
        </SidebarGroup>

        <SidebarGroup className="mt-auto group-data-[collapsible=icon]:hidden">
          <Link href={ROUTES.inventoryNew} className="sidebar-help">
        <div className="sidebar-help-title">
          <IconSparkles size={14} /> Quick action
        </div>
        <div className="sidebar-help-copy">Add a new inventory item using database-backed settings.</div>
        <span className="sidebar-help-button">Add item</span>
      </Link>
        </SidebarGroup>
      </SidebarContent>

      <SidebarFooter className="inventory-sidebar-footer">
        <ShadcnButton type="button" variant="ghost" onClick={handleLogout} className="sidebar-user">
        <Avatar initials={userInitials(user)} size={30} />
        <div className="sidebar-user-meta">
          <div className="sidebar-user-name">{user?.name ?? "Admin User"}</div>
          <div className="sidebar-user-role">Admin</div>
        </div>
        <span className="sidebar-logout-icon"><IconLogOut size={14} /></span>
      </ShadcnButton>
      </SidebarFooter>
      <SidebarRail />
    </ShadcnSidebar>
  );
}
