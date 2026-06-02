"use client";

import { useEffect, useState, type KeyboardEvent } from "react";
import { useRouter } from "next/navigation";
import { useQuery } from "@tanstack/react-query";
import { IconSearch } from "@/components/shared";
import { Button as ShadcnButton } from "@/components/ui/button";
import { Input as ShadcnInput } from "@/components/ui/input";
import { GlobalSearchIcon } from "./GlobalSearchIcon";
import { searchGlobalRecords, type GlobalSearchResult } from "./helpers";

export function GlobalSearch() {
  const router = useRouter();
  const [searchText, setSearchText] = useState("");
  const [debouncedSearch, setDebouncedSearch] = useState("");
  const [focused, setFocused] = useState(false);

  useEffect(() => {
    const handle = window.setTimeout(() => setDebouncedSearch(searchText.trim()), 220);
    return () => window.clearTimeout(handle);
  }, [searchText]);

  const searchQuery = useQuery({
    queryKey: ["global-search", debouncedSearch],
    queryFn: () => searchGlobalRecords(debouncedSearch),
    enabled: debouncedSearch.length >= 2,
    staleTime: 30_000,
  });

  const results = searchQuery.data ?? [];
  const showResults = focused && searchText.trim().length >= 2;

  function openResult(result: GlobalSearchResult) {
    router.push(result.href);
    setSearchText("");
    setDebouncedSearch("");
    setFocused(false);
  }

  function handleKeyDown(event: KeyboardEvent<HTMLInputElement>) {
    if (event.key === "Escape") {
      setFocused(false);
      return;
    }

    if (event.key === "Enter" && results[0]) {
      event.preventDefault();
      openResult(results[0]);
    }
  }

  return (
    <div
      className={`topbar-search-wrap${showResults ? " open" : ""}`}
      onFocus={() => setFocused(true)}
      onBlur={(event) => {
        if (!event.currentTarget.contains(event.relatedTarget)) {
          setFocused(false);
        }
      }}
    >
      <label className="topbar-search" aria-label="Search inventory">
        <IconSearch size={16} />
        <ShadcnInput
          type="search"
          placeholder="Search items, suppliers..."
          value={searchText}
          aria-expanded={showResults}
          aria-controls="global-search-results"
          autoComplete="off"
          onChange={(event) => setSearchText(event.target.value)}
          onKeyDown={handleKeyDown}
        />
      </label>

      {showResults ? (
        <div id="global-search-results" className="global-search-popover" role="listbox">
          {searchQuery.isFetching ? (
            <div className="global-search-message">Searching...</div>
          ) : results.length > 0 ? (
            results.map((result) => (
              <ShadcnButton
                key={result.id}
                type="button"
                variant="ghost"
                className="global-search-result"
                role="option"
                onMouseDown={(event) => event.preventDefault()}
                onClick={() => openResult(result)}
              >
                <span className={`global-search-result-icon ${result.type}`}>
                  <GlobalSearchIcon type={result.type} />
                </span>
                <span className="global-search-result-copy">
                  <span className="global-search-result-title">{result.title}</span>
                  <span className="global-search-result-subtitle">{result.subtitle}</span>
                </span>
                <span className="global-search-result-label">{result.label}</span>
              </ShadcnButton>
            ))
          ) : (
            <div className="global-search-message">No matching records</div>
          )}
        </div>
      ) : null}
    </div>
  );
}
