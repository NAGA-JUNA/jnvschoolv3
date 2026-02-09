import { createContext, useContext, useState, useEffect, ReactNode } from "react";

export interface ThemePreset {
  id: string;
  name: string;
  colors: ThemeColors;
  preview: { sidebar: string; primary: string; accent: string };
}

export interface ThemeColors {
  primary: string;
  "primary-foreground": string;
  accent: string;
  "accent-foreground": string;
  "sidebar-gradient-from": string;
  "sidebar-gradient-to": string;
  "sidebar-background": string;
  "sidebar-foreground": string;
  "sidebar-primary": string;
  "sidebar-primary-foreground": string;
  "sidebar-accent": string;
  "sidebar-border": string;
  "kpi-blue": string;
  "kpi-green": string;
  "kpi-orange": string;
  "kpi-purple": string;
  "kpi-pink": string;
}

export const THEME_PRESETS: ThemePreset[] = [
  {
    id: "royal-blue",
    name: "Royal Blue",
    preview: { sidebar: "#1a2342", primary: "#3b82f6", accent: "#f59e0b" },
    colors: {
      primary: "221 83% 53%",
      "primary-foreground": "0 0% 100%",
      accent: "38 92% 50%",
      "accent-foreground": "0 0% 100%",
      "sidebar-gradient-from": "222 47% 11%",
      "sidebar-gradient-to": "217 60% 20%",
      "sidebar-background": "222 47% 14%",
      "sidebar-foreground": "210 20% 90%",
      "sidebar-primary": "217 91% 60%",
      "sidebar-primary-foreground": "0 0% 100%",
      "sidebar-accent": "222 47% 20%",
      "sidebar-border": "222 30% 22%",
      "kpi-blue": "221 83% 53%",
      "kpi-green": "142 71% 45%",
      "kpi-orange": "25 95% 53%",
      "kpi-purple": "262 83% 58%",
      "kpi-pink": "330 81% 60%",
    },
  },
  {
    id: "forest-green",
    name: "Forest Green",
    preview: { sidebar: "#14332a", primary: "#22c55e", accent: "#f59e0b" },
    colors: {
      primary: "142 71% 45%",
      "primary-foreground": "0 0% 100%",
      accent: "38 92% 50%",
      "accent-foreground": "0 0% 100%",
      "sidebar-gradient-from": "155 40% 10%",
      "sidebar-gradient-to": "155 45% 18%",
      "sidebar-background": "155 40% 13%",
      "sidebar-foreground": "150 20% 90%",
      "sidebar-primary": "142 71% 50%",
      "sidebar-primary-foreground": "0 0% 100%",
      "sidebar-accent": "155 40% 20%",
      "sidebar-border": "155 30% 20%",
      "kpi-blue": "142 71% 45%",
      "kpi-green": "160 70% 40%",
      "kpi-orange": "25 95% 53%",
      "kpi-purple": "262 83% 58%",
      "kpi-pink": "330 81% 60%",
    },
  },
  {
    id: "royal-purple",
    name: "Royal Purple",
    preview: { sidebar: "#2d1b4e", primary: "#8b5cf6", accent: "#f59e0b" },
    colors: {
      primary: "262 83% 58%",
      "primary-foreground": "0 0% 100%",
      accent: "38 92% 50%",
      "accent-foreground": "0 0% 100%",
      "sidebar-gradient-from": "270 40% 12%",
      "sidebar-gradient-to": "262 50% 22%",
      "sidebar-background": "270 40% 15%",
      "sidebar-foreground": "260 20% 90%",
      "sidebar-primary": "262 83% 63%",
      "sidebar-primary-foreground": "0 0% 100%",
      "sidebar-accent": "270 40% 22%",
      "sidebar-border": "270 30% 22%",
      "kpi-blue": "262 83% 58%",
      "kpi-green": "142 71% 45%",
      "kpi-orange": "25 95% 53%",
      "kpi-purple": "280 80% 55%",
      "kpi-pink": "330 81% 60%",
    },
  },
  {
    id: "crimson-red",
    name: "Crimson Red",
    preview: { sidebar: "#3b1420", primary: "#ef4444", accent: "#fbbf24" },
    colors: {
      primary: "0 84% 60%",
      "primary-foreground": "0 0% 100%",
      accent: "45 93% 56%",
      "accent-foreground": "0 0% 10%",
      "sidebar-gradient-from": "345 45% 10%",
      "sidebar-gradient-to": "350 50% 18%",
      "sidebar-background": "345 45% 13%",
      "sidebar-foreground": "0 20% 90%",
      "sidebar-primary": "0 84% 60%",
      "sidebar-primary-foreground": "0 0% 100%",
      "sidebar-accent": "345 45% 20%",
      "sidebar-border": "345 30% 20%",
      "kpi-blue": "0 84% 60%",
      "kpi-green": "142 71% 45%",
      "kpi-orange": "25 95% 53%",
      "kpi-purple": "262 83% 58%",
      "kpi-pink": "330 81% 60%",
    },
  },
  {
    id: "ocean-teal",
    name: "Ocean Teal",
    preview: { sidebar: "#0f2d30", primary: "#14b8a6", accent: "#f59e0b" },
    colors: {
      primary: "173 80% 40%",
      "primary-foreground": "0 0% 100%",
      accent: "38 92% 50%",
      "accent-foreground": "0 0% 100%",
      "sidebar-gradient-from": "180 40% 10%",
      "sidebar-gradient-to": "175 50% 18%",
      "sidebar-background": "180 40% 13%",
      "sidebar-foreground": "175 20% 90%",
      "sidebar-primary": "173 80% 45%",
      "sidebar-primary-foreground": "0 0% 100%",
      "sidebar-accent": "180 40% 20%",
      "sidebar-border": "180 30% 20%",
      "kpi-blue": "173 80% 40%",
      "kpi-green": "142 71% 45%",
      "kpi-orange": "25 95% 53%",
      "kpi-purple": "262 83% 58%",
      "kpi-pink": "330 81% 60%",
    },
  },
  {
    id: "warm-amber",
    name: "Warm Amber",
    preview: { sidebar: "#2d1f0e", primary: "#f59e0b", accent: "#3b82f6" },
    colors: {
      primary: "38 92% 50%",
      "primary-foreground": "0 0% 100%",
      accent: "221 83% 53%",
      "accent-foreground": "0 0% 100%",
      "sidebar-gradient-from": "30 40% 9%",
      "sidebar-gradient-to": "35 50% 16%",
      "sidebar-background": "30 40% 12%",
      "sidebar-foreground": "35 20% 90%",
      "sidebar-primary": "38 92% 55%",
      "sidebar-primary-foreground": "0 0% 100%",
      "sidebar-accent": "30 40% 20%",
      "sidebar-border": "30 30% 18%",
      "kpi-blue": "38 92% 50%",
      "kpi-green": "142 71% 45%",
      "kpi-orange": "25 95% 53%",
      "kpi-purple": "262 83% 58%",
      "kpi-pink": "330 81% 60%",
    },
  },
  {
    id: "slate-modern",
    name: "Slate Modern",
    preview: { sidebar: "#1e293b", primary: "#64748b", accent: "#3b82f6" },
    colors: {
      primary: "215 16% 47%",
      "primary-foreground": "0 0% 100%",
      accent: "221 83% 53%",
      "accent-foreground": "0 0% 100%",
      "sidebar-gradient-from": "217 33% 12%",
      "sidebar-gradient-to": "215 28% 20%",
      "sidebar-background": "217 33% 15%",
      "sidebar-foreground": "215 20% 90%",
      "sidebar-primary": "215 20% 55%",
      "sidebar-primary-foreground": "0 0% 100%",
      "sidebar-accent": "217 33% 22%",
      "sidebar-border": "217 25% 22%",
      "kpi-blue": "221 83% 53%",
      "kpi-green": "142 71% 45%",
      "kpi-orange": "25 95% 53%",
      "kpi-purple": "262 83% 58%",
      "kpi-pink": "330 81% 60%",
    },
  },
  {
    id: "rose-pink",
    name: "Rose Pink",
    preview: { sidebar: "#3b1530", primary: "#ec4899", accent: "#f59e0b" },
    colors: {
      primary: "330 81% 60%",
      "primary-foreground": "0 0% 100%",
      accent: "38 92% 50%",
      "accent-foreground": "0 0% 100%",
      "sidebar-gradient-from": "330 40% 10%",
      "sidebar-gradient-to": "335 50% 18%",
      "sidebar-background": "330 40% 13%",
      "sidebar-foreground": "330 20% 90%",
      "sidebar-primary": "330 81% 60%",
      "sidebar-primary-foreground": "0 0% 100%",
      "sidebar-accent": "330 40% 20%",
      "sidebar-border": "330 30% 20%",
      "kpi-blue": "330 81% 60%",
      "kpi-green": "142 71% 45%",
      "kpi-orange": "25 95% 53%",
      "kpi-purple": "262 83% 58%",
      "kpi-pink": "340 75% 55%",
    },
  },
];

interface BrandingState {
  logoUrl: string | null;
  schoolName: string;
  tagline: string;
  activePresetId: string;
  customColors: Partial<ThemeColors> | null;
  useCustomColors: boolean;
}

interface ThemeContextType {
  branding: BrandingState;
  activeColors: ThemeColors;
  setLogo: (url: string | null) => void;
  setSchoolName: (name: string) => void;
  setTagline: (tagline: string) => void;
  setPreset: (presetId: string) => void;
  setCustomColors: (colors: Partial<ThemeColors>) => void;
  setUseCustomColors: (use: boolean) => void;
  resetToDefaults: () => void;
}

const DEFAULT_BRANDING: BrandingState = {
  logoUrl: null,
  schoolName: "JSchoolAdmin",
  tagline: "Modern School Management",
  activePresetId: "royal-blue",
  customColors: null,
  useCustomColors: false,
};

const ThemeContext = createContext<ThemeContextType | undefined>(undefined);

function applyThemeToDOM(colors: ThemeColors) {
  const root = document.documentElement;
  Object.entries(colors).forEach(([key, value]) => {
    root.style.setProperty(`--${key}`, value);
  });
}

function loadBranding(): BrandingState {
  try {
    const saved = localStorage.getItem("school-branding");
    if (saved) return { ...DEFAULT_BRANDING, ...JSON.parse(saved) };
  } catch {}
  return DEFAULT_BRANDING;
}

function saveBranding(state: BrandingState) {
  localStorage.setItem("school-branding", JSON.stringify(state));
}

export function ThemeProvider({ children }: { children: ReactNode }) {
  const [branding, setBranding] = useState<BrandingState>(loadBranding);

  const activeColors: ThemeColors = branding.useCustomColors && branding.customColors
    ? { ...THEME_PRESETS[0].colors, ...branding.customColors }
    : (THEME_PRESETS.find((p) => p.id === branding.activePresetId) || THEME_PRESETS[0]).colors;

  // Apply theme on change
  useEffect(() => {
    applyThemeToDOM(activeColors);
    saveBranding(branding);
  }, [branding, activeColors]);

  const setLogo = (url: string | null) =>
    setBranding((prev) => ({ ...prev, logoUrl: url }));

  const setSchoolName = (name: string) =>
    setBranding((prev) => ({ ...prev, schoolName: name }));

  const setTagline = (tagline: string) =>
    setBranding((prev) => ({ ...prev, tagline }));

  const setPreset = (presetId: string) =>
    setBranding((prev) => ({ ...prev, activePresetId: presetId, useCustomColors: false }));

  const setCustomColors = (colors: Partial<ThemeColors>) =>
    setBranding((prev) => ({
      ...prev,
      customColors: { ...(prev.customColors || {}), ...colors },
      useCustomColors: true,
    }));

  const setUseCustomColors = (use: boolean) =>
    setBranding((prev) => ({ ...prev, useCustomColors: use }));

  const resetToDefaults = () => {
    setBranding(DEFAULT_BRANDING);
    localStorage.removeItem("school-branding");
  };

  return (
    <ThemeContext.Provider value={{
      branding,
      activeColors,
      setLogo,
      setSchoolName,
      setTagline,
      setPreset,
      setCustomColors,
      setUseCustomColors,
      resetToDefaults,
    }}>
      {children}
    </ThemeContext.Provider>
  );
}

export function useTheme() {
  const ctx = useContext(ThemeContext);
  if (!ctx) throw new Error("useTheme must be used within ThemeProvider");
  return ctx;
}
