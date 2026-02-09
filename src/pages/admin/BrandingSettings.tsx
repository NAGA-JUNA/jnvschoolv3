import { useState, useRef } from "react";
import { Upload, Check, RotateCcw, Palette, Image as ImageIcon, Type } from "lucide-react";
import { useTheme, THEME_PRESETS, ThemeColors } from "@/contexts/ThemeContext";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { cn } from "@/lib/utils";
import { toast } from "@/hooks/use-toast";

function hslToHex(hsl: string): string {
  const parts = hsl.split(" ").map((v) => parseFloat(v));
  if (parts.length < 3) return "#3b82f6";
  const [h, s, l] = [parts[0], parts[1] / 100, parts[2] / 100];
  const a = s * Math.min(l, 1 - l);
  const f = (n: number) => {
    const k = (n + h / 30) % 12;
    const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
    return Math.round(255 * color).toString(16).padStart(2, "0");
  };
  return `#${f(0)}${f(8)}${f(4)}`;
}

function hexToHsl(hex: string): string {
  let r = 0, g = 0, b = 0;
  if (hex.length === 4) {
    r = parseInt(hex[1] + hex[1], 16);
    g = parseInt(hex[2] + hex[2], 16);
    b = parseInt(hex[3] + hex[3], 16);
  } else if (hex.length === 7) {
    r = parseInt(hex.slice(1, 3), 16);
    g = parseInt(hex.slice(3, 5), 16);
    b = parseInt(hex.slice(5, 7), 16);
  }
  r /= 255; g /= 255; b /= 255;
  const max = Math.max(r, g, b), min = Math.min(r, g, b);
  let h = 0, s = 0;
  const l = (max + min) / 2;
  if (max !== min) {
    const d = max - min;
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
    switch (max) {
      case r: h = ((g - b) / d + (g < b ? 6 : 0)) / 6; break;
      case g: h = ((b - r) / d + 2) / 6; break;
      case b: h = ((r - g) / d + 4) / 6; break;
    }
  }
  return `${Math.round(h * 360)} ${Math.round(s * 100)}% ${Math.round(l * 100)}%`;
}

interface ColorPickerFieldProps {
  label: string;
  hslValue: string;
  onChange: (hsl: string) => void;
}

function ColorPickerField({ label, hslValue, onChange }: ColorPickerFieldProps) {
  return (
    <div className="flex items-center gap-3">
      <input
        type="color"
        value={hslToHex(hslValue)}
        onChange={(e) => onChange(hexToHsl(e.target.value))}
        className="w-10 h-10 rounded-lg border border-border cursor-pointer"
      />
      <div className="flex-1">
        <p className="text-sm font-medium text-foreground">{label}</p>
        <p className="text-xs text-muted-foreground font-mono">{hslValue}</p>
      </div>
    </div>
  );
}

export default function BrandingSettings() {
  const {
    branding, activeColors,
    setLogo, setSchoolName, setTagline,
    setPreset, setCustomColors, setUseCustomColors,
    resetToDefaults,
  } = useTheme();

  const fileInputRef = useRef<HTMLInputElement>(null);
  const [localName, setLocalName] = useState(branding.schoolName);
  const [localTagline, setLocalTagline] = useState(branding.tagline);

  const handleLogoUpload = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    if (file.size > 2 * 1024 * 1024) {
      toast({ title: "File too large", description: "Logo must be under 2MB", variant: "destructive" });
      return;
    }

    const reader = new FileReader();
    reader.onload = (ev) => {
      const url = ev.target?.result as string;
      setLogo(url);
      toast({ title: "Logo updated", description: "Your school logo has been updated successfully." });
    };
    reader.readAsDataURL(file);
  };

  const handleSaveInfo = () => {
    setSchoolName(localName);
    setTagline(localTagline);
    toast({ title: "Branding updated", description: "School name and tagline saved." });
  };

  const handleCustomColorChange = (key: keyof ThemeColors, hsl: string) => {
    setCustomColors({ [key]: hsl });
  };

  const customBase: ThemeColors = branding.useCustomColors && branding.customColors
    ? { ...THEME_PRESETS[0].colors, ...branding.customColors }
    : activeColors;

  return (
    <div className="space-y-6 max-w-4xl">
      <div>
        <h1 className="text-2xl font-bold tracking-tight text-foreground">Branding & Theme</h1>
        <p className="text-muted-foreground text-sm mt-1">
          Customize your school's logo, name, and dashboard color theme.
        </p>
      </div>

      <Tabs defaultValue="logo" className="space-y-4">
        <TabsList className="grid w-full grid-cols-3">
          <TabsTrigger value="logo" className="gap-2">
            <ImageIcon className="h-4 w-4" /> Logo & Info
          </TabsTrigger>
          <TabsTrigger value="presets" className="gap-2">
            <Palette className="h-4 w-4" /> Color Presets
          </TabsTrigger>
          <TabsTrigger value="custom" className="gap-2">
            <Type className="h-4 w-4" /> Custom Colors
          </TabsTrigger>
        </TabsList>

        {/* Logo & School Info */}
        <TabsContent value="logo">
          <Card>
            <CardHeader>
              <CardTitle>School Logo & Information</CardTitle>
              <CardDescription>Upload your school logo and set the name that appears in the sidebar and footer.</CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              {/* Logo Upload */}
              <div className="space-y-3">
                <Label>School Logo</Label>
                <div className="flex items-start gap-6">
                  <div
                    className="w-28 h-28 rounded-2xl border-2 border-dashed border-border flex items-center justify-center bg-muted/30 overflow-hidden cursor-pointer hover:border-primary/50 transition-colors"
                    onClick={() => fileInputRef.current?.click()}
                  >
                    {branding.logoUrl ? (
                      <img src={branding.logoUrl} alt="School logo" className="w-full h-full object-contain p-2" />
                    ) : (
                      <div className="text-center">
                        <Upload className="h-6 w-6 mx-auto text-muted-foreground mb-1" />
                        <p className="text-[10px] text-muted-foreground">Upload</p>
                      </div>
                    )}
                  </div>
                  <input ref={fileInputRef} type="file" accept="image/*" className="hidden" onChange={handleLogoUpload} />
                  <div className="flex-1 space-y-2">
                    <p className="text-sm text-muted-foreground">
                      Upload a PNG or SVG logo. Max 2MB. Recommended size: 200×200px.
                    </p>
                    <div className="flex gap-2">
                      <Button size="sm" variant="outline" onClick={() => fileInputRef.current?.click()}>
                        <Upload className="h-3.5 w-3.5 mr-1.5" /> Upload Logo
                      </Button>
                      {branding.logoUrl && (
                        <Button size="sm" variant="ghost" className="text-destructive" onClick={() => setLogo(null)}>
                          Remove
                        </Button>
                      )}
                    </div>
                  </div>
                </div>
              </div>

              {/* School Name */}
              <div className="space-y-2">
                <Label htmlFor="school-name">School Name</Label>
                <Input id="school-name" value={localName} onChange={(e) => setLocalName(e.target.value)} placeholder="Enter school name" />
              </div>

              {/* Tagline */}
              <div className="space-y-2">
                <Label htmlFor="tagline">Tagline</Label>
                <Input id="tagline" value={localTagline} onChange={(e) => setLocalTagline(e.target.value)} placeholder="Enter tagline" />
              </div>

              <Button onClick={handleSaveInfo}>
                <Check className="h-4 w-4 mr-1.5" /> Save Changes
              </Button>
            </CardContent>
          </Card>
        </TabsContent>

        {/* Color Presets */}
        <TabsContent value="presets">
          <Card>
            <CardHeader>
              <CardTitle>Color Presets</CardTitle>
              <CardDescription>Choose a preset color theme that matches your school's branding.</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                {THEME_PRESETS.map((preset) => {
                  const isActive = !branding.useCustomColors && branding.activePresetId === preset.id;
                  return (
                    <button
                      key={preset.id}
                      onClick={() => setPreset(preset.id)}
                      className={cn(
                        "relative flex flex-col items-center gap-3 p-4 rounded-xl border-2 transition-all duration-200 hover:shadow-md",
                        isActive
                          ? "border-primary bg-primary/5 shadow-md"
                          : "border-border hover:border-primary/30"
                      )}
                    >
                      {isActive && (
                        <div className="absolute top-2 right-2 bg-primary rounded-full p-0.5">
                          <Check className="h-3 w-3 text-primary-foreground" />
                        </div>
                      )}
                      {/* Color preview */}
                      <div className="flex gap-1.5">
                        <div className="w-8 h-8 rounded-lg shadow-sm" style={{ background: preset.preview.sidebar }} />
                        <div className="w-8 h-8 rounded-lg shadow-sm" style={{ background: preset.preview.primary }} />
                        <div className="w-8 h-8 rounded-lg shadow-sm" style={{ background: preset.preview.accent }} />
                      </div>
                      <span className="text-xs font-medium text-foreground">{preset.name}</span>
                    </button>
                  );
                })}
              </div>
            </CardContent>
          </Card>
        </TabsContent>

        {/* Custom Colors */}
        <TabsContent value="custom">
          <Card>
            <CardHeader>
              <CardTitle>Custom Colors</CardTitle>
              <CardDescription>Fine-tune individual colors for a fully custom theme. Pick colors that match your school logo.</CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="flex items-center gap-3 p-3 rounded-lg bg-muted/50 border border-border">
                <input
                  type="checkbox"
                  id="use-custom"
                  checked={branding.useCustomColors}
                  onChange={(e) => setUseCustomColors(e.target.checked)}
                  className="h-4 w-4 rounded border-border"
                />
                <Label htmlFor="use-custom" className="text-sm cursor-pointer">
                  Enable custom colors (overrides preset)
                </Label>
              </div>

              <div className={cn("space-y-6 transition-opacity", !branding.useCustomColors && "opacity-40 pointer-events-none")}>
                {/* Primary & Accent */}
                <div>
                  <h4 className="text-sm font-semibold text-foreground mb-3">Main Colors</h4>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <ColorPickerField label="Primary Color" hslValue={customBase.primary} onChange={(v) => handleCustomColorChange("primary", v)} />
                    <ColorPickerField label="Accent Color" hslValue={customBase.accent} onChange={(v) => handleCustomColorChange("accent", v)} />
                  </div>
                </div>

                {/* Sidebar Colors */}
                <div>
                  <h4 className="text-sm font-semibold text-foreground mb-3">Sidebar</h4>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <ColorPickerField label="Sidebar Top (Gradient)" hslValue={customBase["sidebar-gradient-from"]} onChange={(v) => handleCustomColorChange("sidebar-gradient-from", v)} />
                    <ColorPickerField label="Sidebar Bottom (Gradient)" hslValue={customBase["sidebar-gradient-to"]} onChange={(v) => handleCustomColorChange("sidebar-gradient-to", v)} />
                    <ColorPickerField label="Active Link" hslValue={customBase["sidebar-primary"]} onChange={(v) => handleCustomColorChange("sidebar-primary", v)} />
                    <ColorPickerField label="Sidebar Border" hslValue={customBase["sidebar-border"]} onChange={(v) => handleCustomColorChange("sidebar-border", v)} />
                  </div>
                </div>

                {/* KPI Colors */}
                <div>
                  <h4 className="text-sm font-semibold text-foreground mb-3">Dashboard Card Icons</h4>
                  <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <ColorPickerField label="Blue KPI" hslValue={customBase["kpi-blue"]} onChange={(v) => handleCustomColorChange("kpi-blue", v)} />
                    <ColorPickerField label="Green KPI" hslValue={customBase["kpi-green"]} onChange={(v) => handleCustomColorChange("kpi-green", v)} />
                    <ColorPickerField label="Orange KPI" hslValue={customBase["kpi-orange"]} onChange={(v) => handleCustomColorChange("kpi-orange", v)} />
                    <ColorPickerField label="Purple KPI" hslValue={customBase["kpi-purple"]} onChange={(v) => handleCustomColorChange("kpi-purple", v)} />
                    <ColorPickerField label="Pink KPI" hslValue={customBase["kpi-pink"]} onChange={(v) => handleCustomColorChange("kpi-pink", v)} />
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      {/* Reset button */}
      <div className="flex justify-end">
        <Button variant="outline" className="text-destructive border-destructive/30 hover:bg-destructive/5" onClick={resetToDefaults}>
          <RotateCcw className="h-4 w-4 mr-1.5" /> Reset to Defaults
        </Button>
      </div>
    </div>
  );
}
