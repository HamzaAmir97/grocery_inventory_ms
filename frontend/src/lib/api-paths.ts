export const API_PATHS = {
  AUTH: {
    LOGIN: "/auth/login",
    LOGOUT: "/auth/logout",
    ME: "/auth/me",
  },

  DASHBOARD: {
    STATS: "/dashboard/stats",
  },

  INVENTORY: {
    LIST: "/items",
    CREATE: "/items",
    DETAIL: (id: string | number) => `/items/${encodeURIComponent(String(id))}`,
    UPDATE: (id: string | number) => `/items/${encodeURIComponent(String(id))}`,
    DELETE: (id: string | number) => `/items/${encodeURIComponent(String(id))}`,
  },

  SETTINGS: {
    CATEGORIES: {
      LIST: "/categories",
      CREATE: "/categories",
      DETAIL: (id: string | number) => `/categories/${encodeURIComponent(String(id))}`,
      UPDATE: (id: string | number) => `/categories/${encodeURIComponent(String(id))}`,
      DELETE: (id: string | number) => `/categories/${encodeURIComponent(String(id))}`,
    },
    SUBCATEGORIES: {
      LIST: "/subcategories",
      CREATE: "/subcategories",
      DETAIL: (id: string | number) => `/subcategories/${encodeURIComponent(String(id))}`,
      UPDATE: (id: string | number) => `/subcategories/${encodeURIComponent(String(id))}`,
      DELETE: (id: string | number) => `/subcategories/${encodeURIComponent(String(id))}`,
    },
    UNITS: {
      LIST: "/units",
      CREATE: "/units",
      DETAIL: (id: string | number) => `/units/${encodeURIComponent(String(id))}`,
      UPDATE: (id: string | number) => `/units/${encodeURIComponent(String(id))}`,
      DELETE: (id: string | number) => `/units/${encodeURIComponent(String(id))}`,
    },
    SUPPLIERS: {
      LIST: "/suppliers",
      CREATE: "/suppliers",
      DETAIL: (id: string | number) => `/suppliers/${encodeURIComponent(String(id))}`,
      UPDATE: (id: string | number) => `/suppliers/${encodeURIComponent(String(id))}`,
      DELETE: (id: string | number) => `/suppliers/${encodeURIComponent(String(id))}`,
    },
  },

  LOOKUPS: {
    CATEGORIES: "/lookups/categories",
    SUBCATEGORIES: "/lookups/subcategories",
    UNITS: "/lookups/units",
    SUPPLIERS: "/lookups/suppliers",
  },
} as const;
