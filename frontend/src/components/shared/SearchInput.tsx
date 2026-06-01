import type { InputHTMLAttributes } from "react";
import { Input } from "./Input";

function IconSearchGlyph() {
  // Lightweight import to avoid circular noise; mirrors the shared IconSearch.
  return (
    <svg width={16} height={16} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth={1.75} strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
      <circle cx="11" cy="11" r="8" />
      <line x1="21" y1="21" x2="16.65" y2="16.65" />
    </svg>
  );
}

export function SearchInput(props: InputHTMLAttributes<HTMLInputElement>) {
  return <Input icon={<IconSearchGlyph />} placeholder="Search by name or SKU" {...props} />;
}
