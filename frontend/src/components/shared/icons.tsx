import type { CSSProperties, ReactNode } from "react";

// Lucide-style stroke icons, inlined to match the design system (stroke 1.75).
type IconProps = { size?: number; stroke?: number; style?: CSSProperties; className?: string };

function Ic({ size = 18, stroke = 1.75, style, className, children }: IconProps & { children: ReactNode }) {
  return (
    <svg
      width={size}
      height={size}
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth={stroke}
      strokeLinecap="round"
      strokeLinejoin="round"
      style={style}
      className={className}
      aria-hidden="true"
    >
      {children}
    </svg>
  );
}

export const IconDashboard = (p: IconProps) => (
  <Ic {...p}><rect x="3" y="3" width="7" height="9" /><rect x="14" y="3" width="7" height="5" /><rect x="14" y="12" width="7" height="9" /><rect x="3" y="16" width="7" height="5" /></Ic>
);
export const IconBox = (p: IconProps) => (
  <Ic {...p}><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" /><polyline points="3.27 6.96 12 12.01 20.73 6.96" /><line x1="12" y1="22.08" x2="12" y2="12" /></Ic>
);
export const IconCategory = (p: IconProps) => (
  <Ic {...p}><rect x="3" y="3" width="7" height="7" /><rect x="14" y="3" width="7" height="7" /><rect x="14" y="14" width="7" height="7" /><rect x="3" y="14" width="7" height="7" /></Ic>
);
export const IconRuler = (p: IconProps) => (
  <Ic {...p}><path d="M21.3 8.7L8.7 21.3a1 1 0 0 1-1.4 0L2.7 16.7a1 1 0 0 1 0-1.4L15.3 2.7a1 1 0 0 1 1.4 0l4.6 4.6a1 1 0 0 1 0 1.4z" /><path d="M7 17l-2-2" /><path d="M11 13l-2-2" /><path d="M15 9l-2-2" /></Ic>
);
export const IconTruck = (p: IconProps) => (
  <Ic {...p}><rect x="1" y="3" width="15" height="13" /><polygon points="16 8 20 8 23 11 23 16 16 16 16 8" /><circle cx="5.5" cy="18.5" r="2.5" /><circle cx="18.5" cy="18.5" r="2.5" /></Ic>
);
export const IconDatabase = (p: IconProps) => (
  <Ic {...p}><ellipse cx="12" cy="5" rx="8" ry="3" /><path d="M4 5v6c0 1.66 3.58 3 8 3s8-1.34 8-3V5" /><path d="M4 11v6c0 1.66 3.58 3 8 3s8-1.34 8-3v-6" /></Ic>
);
export const IconSettings = (p: IconProps) => (
  <Ic {...p}><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" /></Ic>
);
export const IconSearch = (p: IconProps) => (
  <Ic {...p}><circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" /></Ic>
);
export const IconPlus = (p: IconProps) => (
  <Ic {...p}><line x1="12" y1="5" x2="12" y2="19" /><line x1="5" y1="12" x2="19" y2="12" /></Ic>
);
export const IconCheck = (p: IconProps) => (
  <Ic {...p}><polyline points="20 6 9 17 4 12" /></Ic>
);
export const IconClose = (p: IconProps) => (
  <Ic {...p}><line x1="18" y1="6" x2="6" y2="18" /><line x1="6" y1="6" x2="18" y2="18" /></Ic>
);
export const IconEdit = (p: IconProps) => (
  <Ic {...p}><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" /><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" /></Ic>
);
export const IconTrash = (p: IconProps) => (
  <Ic {...p}><polyline points="3 6 5 6 21 6" /><path d="M19 6l-2 14a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2L5 6" /><path d="M10 11v6" /><path d="M14 11v6" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></Ic>
);
export const IconAlert = (p: IconProps) => (
  <Ic {...p}><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2" /><line x1="12" y1="8" x2="12" y2="12" /><line x1="12" y1="16" x2="12.01" y2="16" /></Ic>
);
export const IconAlertTriangle = (p: IconProps) => (
  <Ic {...p}><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" /></Ic>
);
export const IconBell = (p: IconProps) => (
  <Ic {...p}><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" /><path d="M13.73 21a2 2 0 0 1-3.46 0" /></Ic>
);
export const IconChevronDown = (p: IconProps) => (
  <Ic {...p}><polyline points="6 9 12 15 18 9" /></Ic>
);
export const IconChevronRight = (p: IconProps) => (
  <Ic {...p}><polyline points="9 18 15 12 9 6" /></Ic>
);
export const IconChevronLeft = (p: IconProps) => (
  <Ic {...p}><polyline points="15 18 9 12 15 6" /></Ic>
);
export const IconMore = (p: IconProps) => (
  <Ic {...p}><circle cx="12" cy="12" r="1" /><circle cx="19" cy="12" r="1" /><circle cx="5" cy="12" r="1" /></Ic>
);
export const IconTrending = (p: IconProps) => (
  <Ic {...p}><polyline points="23 6 13.5 15.5 8.5 10.5 1 18" /><polyline points="17 6 23 6 23 12" /></Ic>
);
export const IconUsers = (p: IconProps) => (
  <Ic {...p}><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M23 21v-2a4 4 0 0 0-3-3.87" /><path d="M16 3.13a4 4 0 0 1 0 7.75" /></Ic>
);
export const IconDollar = (p: IconProps) => (
  <Ic {...p}><line x1="12" y1="1" x2="12" y2="23" /><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" /></Ic>
);
export const IconFilter = (p: IconProps) => (
  <Ic {...p}><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" /></Ic>
);
export const IconLogOut = (p: IconProps) => (
  <Ic {...p}><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><polyline points="16 17 21 12 16 7" /><line x1="21" y1="12" x2="9" y2="12" /></Ic>
);
export const IconSparkles = (p: IconProps) => (
  <Ic {...p}><path d="M12 3l1.5 4.5L18 9l-4.5 1.5L12 15l-1.5-4.5L6 9l4.5-1.5L12 3z" /></Ic>
);
export const IconMenu = (p: IconProps) => (
  <Ic {...p}><line x1="3" y1="6" x2="21" y2="6" /><line x1="3" y1="12" x2="21" y2="12" /><line x1="3" y1="18" x2="21" y2="18" /></Ic>
);
export const IconMail = (p: IconProps) => (
  <Ic {...p}><rect x="2" y="4" width="20" height="16" rx="2" /><polyline points="22 6 12 13 2 6" /></Ic>
);
export const IconPhone = (p: IconProps) => (
  <Ic {...p}><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" /></Ic>
);
export const IconLock = (p: IconProps) => (
  <Ic {...p}><rect x="3" y="11" width="18" height="11" rx="2" /><path d="M7 11V7a5 5 0 0 1 10 0v4" /></Ic>
);
export const IconEye = (p: IconProps) => (
  <Ic {...p}><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6-10-6-10-6z" /><circle cx="12" cy="12" r="2.5" /></Ic>
);
export const IconArrowRight = (p: IconProps) => (
  <Ic {...p}><line x1="5" y1="12" x2="19" y2="12" /><polyline points="12 5 19 12 12 19" /></Ic>
);
