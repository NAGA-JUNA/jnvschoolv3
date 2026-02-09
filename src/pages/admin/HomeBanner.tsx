import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Switch } from "@/components/ui/switch";
import { Badge } from "@/components/ui/badge";
import { Textarea } from "@/components/ui/textarea";
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import {
  Plus,
  Pencil,
  Trash2,
  GripVertical,
  Eye,
  EyeOff,
  ArrowRight,
  Image as ImageIcon,
  ChevronUp,
  ChevronDown,
} from "lucide-react";
import { toast } from "sonner";
import { SliderSlide, mockSliderSlides } from "@/data/mockSliderData";
import { cn } from "@/lib/utils";

const emptySlide: Omit<SliderSlide, "id" | "sort_order"> = {
  title: "",
  subtitle: "",
  badge_text: "",
  cta_primary_text: "Apply Now",
  cta_primary_link: "/admissions",
  cta_secondary_text: "Learn More",
  cta_secondary_link: "/about",
  image_url: "",
  is_active: true,
};

export default function HomeBannerPage() {
  const [slides, setSlides] = useState<SliderSlide[]>(
    [...mockSliderSlides].sort((a, b) => a.sort_order - b.sort_order)
  );
  const [editSlide, setEditSlide] = useState<SliderSlide | null>(null);
  const [isNew, setIsNew] = useState(false);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [previewSlide, setPreviewSlide] = useState<SliderSlide | null>(null);

  const nextId = () => Math.max(0, ...slides.map((s) => s.id)) + 1;

  const openAdd = () => {
    setEditSlide({
      ...emptySlide,
      id: nextId(),
      sort_order: slides.length + 1,
    } as SliderSlide);
    setIsNew(true);
    setDialogOpen(true);
  };

  const openEdit = (slide: SliderSlide) => {
    setEditSlide({ ...slide });
    setIsNew(false);
    setDialogOpen(true);
  };

  const handleSave = () => {
    if (!editSlide) return;
    if (!editSlide.title.trim() || !editSlide.image_url.trim()) {
      toast.error("Title and image URL are required");
      return;
    }
    if (isNew) {
      setSlides((prev) => [...prev, editSlide]);
      toast.success("Slide added successfully");
    } else {
      setSlides((prev) => prev.map((s) => (s.id === editSlide.id ? editSlide : s)));
      toast.success("Slide updated successfully");
    }
    setDialogOpen(false);
    setEditSlide(null);
  };

  const handleDelete = (id: number) => {
    setSlides((prev) =>
      prev
        .filter((s) => s.id !== id)
        .map((s, i) => ({ ...s, sort_order: i + 1 }))
    );
    toast.success("Slide deleted");
  };

  const toggleActive = (id: number) => {
    setSlides((prev) =>
      prev.map((s) => (s.id === id ? { ...s, is_active: !s.is_active } : s))
    );
  };

  const moveSlide = (index: number, dir: "up" | "down") => {
    const newSlides = [...slides];
    const target = dir === "up" ? index - 1 : index + 1;
    if (target < 0 || target >= newSlides.length) return;
    [newSlides[index], newSlides[target]] = [newSlides[target], newSlides[index]];
    setSlides(newSlides.map((s, i) => ({ ...s, sort_order: i + 1 })));
  };

  return (
    <div className="space-y-6">
      <PageHeader
        title="Home Banner / Slider"
        description="Manage the hero slider on the public homepage. Add, reorder, or remove slides."
      />

      <div className="flex justify-end">
        <Button onClick={openAdd}>
          <Plus className="h-4 w-4 mr-2" /> Add Slide
        </Button>
      </div>

      {/* Slide List */}
      <div className="space-y-3">
        {slides.map((slide, index) => (
          <Card
            key={slide.id}
            className={cn(
              "p-4 flex items-center gap-4 transition-opacity",
              !slide.is_active && "opacity-60"
            )}
          >
            {/* Reorder */}
            <div className="flex flex-col gap-1 shrink-0">
              <button
                onClick={() => moveSlide(index, "up")}
                disabled={index === 0}
                className="text-muted-foreground hover:text-foreground disabled:opacity-30"
              >
                <ChevronUp className="h-4 w-4" />
              </button>
              <GripVertical className="h-4 w-4 text-muted-foreground/40" />
              <button
                onClick={() => moveSlide(index, "down")}
                disabled={index === slides.length - 1}
                className="text-muted-foreground hover:text-foreground disabled:opacity-30"
              >
                <ChevronDown className="h-4 w-4" />
              </button>
            </div>

            {/* Thumbnail */}
            <div className="w-32 h-20 rounded-lg overflow-hidden bg-muted shrink-0">
              {slide.image_url ? (
                <img
                  src={slide.image_url}
                  alt={slide.title}
                  className="w-full h-full object-cover"
                />
              ) : (
                <div className="w-full h-full flex items-center justify-center">
                  <ImageIcon className="h-8 w-8 text-muted-foreground/30" />
                </div>
              )}
            </div>

            {/* Info */}
            <div className="flex-1 min-w-0">
              <div className="flex items-center gap-2 mb-1">
                <h3 className="font-semibold truncate">
                  {slide.title.replace("\n", " ")}
                </h3>
                <Badge
                  variant={slide.is_active ? "default" : "secondary"}
                  className="text-xs shrink-0"
                >
                  {slide.is_active ? "Active" : "Inactive"}
                </Badge>
              </div>
              <p className="text-sm text-muted-foreground truncate">{slide.subtitle}</p>
              <p className="text-xs text-muted-foreground mt-1">
                Order: {slide.sort_order} • Badge: {slide.badge_text}
              </p>
            </div>

            {/* Actions */}
            <div className="flex items-center gap-2 shrink-0">
              <Button
                variant="ghost"
                size="icon"
                onClick={() => setPreviewSlide(slide)}
                title="Preview"
              >
                <Eye className="h-4 w-4" />
              </Button>
              <Button
                variant="ghost"
                size="icon"
                onClick={() => toggleActive(slide.id)}
                title={slide.is_active ? "Disable" : "Enable"}
              >
                {slide.is_active ? (
                  <EyeOff className="h-4 w-4" />
                ) : (
                  <Eye className="h-4 w-4" />
                )}
              </Button>
              <Button
                variant="ghost"
                size="icon"
                onClick={() => openEdit(slide)}
              >
                <Pencil className="h-4 w-4" />
              </Button>
              <Button
                variant="ghost"
                size="icon"
                onClick={() => handleDelete(slide.id)}
                className="text-destructive hover:text-destructive"
              >
                <Trash2 className="h-4 w-4" />
              </Button>
            </div>
          </Card>
        ))}

        {slides.length === 0 && (
          <Card className="p-12 text-center text-muted-foreground">
            <ImageIcon className="h-12 w-12 mx-auto mb-4 opacity-30" />
            <p>No slides yet. Click "Add Slide" to create your first banner.</p>
          </Card>
        )}
      </div>

      {/* Edit / Add Dialog */}
      <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
        <DialogContent className="max-w-2xl max-h-[90vh] overflow-y-auto">
          <DialogHeader>
            <DialogTitle>{isNew ? "Add New Slide" : "Edit Slide"}</DialogTitle>
          </DialogHeader>
          {editSlide && (
            <div className="space-y-4">
              <div>
                <Label>Background Image URL</Label>
                <Input
                  value={editSlide.image_url}
                  onChange={(e) =>
                    setEditSlide({ ...editSlide, image_url: e.target.value })
                  }
                  placeholder="https://example.com/banner.jpg (1920×900 recommended)"
                />
                <p className="text-xs text-muted-foreground mt-1">
                  Recommended: 1920×900px, max 2MB. Use image URL or upload path.
                </p>
              </div>

              <div>
                <Label>Title</Label>
                <Textarea
                  value={editSlide.title}
                  onChange={(e) =>
                    setEditSlide({ ...editSlide, title: e.target.value })
                  }
                  placeholder="Welcome to\nJNV Model School"
                  rows={2}
                />
                <p className="text-xs text-muted-foreground mt-1">
                  Use new line for multi-line titles
                </p>
              </div>

              <div>
                <Label>Subtitle</Label>
                <Input
                  value={editSlide.subtitle}
                  onChange={(e) =>
                    setEditSlide({ ...editSlide, subtitle: e.target.value })
                  }
                  placeholder="Excellence in Education, Character in Action"
                />
              </div>

              <div>
                <Label>Badge Text</Label>
                <Input
                  value={editSlide.badge_text}
                  onChange={(e) =>
                    setEditSlide({ ...editSlide, badge_text: e.target.value })
                  }
                  placeholder="CBSE Affiliated • Est. 2005"
                />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label>Primary CTA Text</Label>
                  <Input
                    value={editSlide.cta_primary_text}
                    onChange={(e) =>
                      setEditSlide({
                        ...editSlide,
                        cta_primary_text: e.target.value,
                      })
                    }
                  />
                </div>
                <div>
                  <Label>Primary CTA Link</Label>
                  <Input
                    value={editSlide.cta_primary_link}
                    onChange={(e) =>
                      setEditSlide({
                        ...editSlide,
                        cta_primary_link: e.target.value,
                      })
                    }
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div>
                  <Label>Secondary CTA Text</Label>
                  <Input
                    value={editSlide.cta_secondary_text}
                    onChange={(e) =>
                      setEditSlide({
                        ...editSlide,
                        cta_secondary_text: e.target.value,
                      })
                    }
                  />
                </div>
                <div>
                  <Label>Secondary CTA Link</Label>
                  <Input
                    value={editSlide.cta_secondary_link}
                    onChange={(e) =>
                      setEditSlide({
                        ...editSlide,
                        cta_secondary_link: e.target.value,
                      })
                    }
                  />
                </div>
              </div>

              <div className="flex items-center gap-3">
                <Switch
                  checked={editSlide.is_active}
                  onCheckedChange={(val) =>
                    setEditSlide({ ...editSlide, is_active: val })
                  }
                />
                <Label>Active</Label>
              </div>

              {/* Mini preview */}
              {editSlide.image_url && (
                <div className="relative h-40 rounded-lg overflow-hidden">
                  <img
                    src={editSlide.image_url}
                    alt="Preview"
                    className="w-full h-full object-cover"
                  />
                  <div className="absolute inset-0 bg-gradient-to-br from-primary/80 via-primary/70 to-primary/60 flex items-center justify-center">
                    <div className="text-center text-primary-foreground space-y-1">
                      <p className="text-xs opacity-80">{editSlide.badge_text}</p>
                      <p className="font-bold text-lg whitespace-pre-line leading-tight">
                        {editSlide.title}
                      </p>
                      <p className="text-xs opacity-80">{editSlide.subtitle}</p>
                    </div>
                  </div>
                </div>
              )}

              <div className="flex justify-end gap-3 pt-2">
                <Button variant="outline" onClick={() => setDialogOpen(false)}>
                  Cancel
                </Button>
                <Button onClick={handleSave}>
                  {isNew ? "Add Slide" : "Save Changes"}
                </Button>
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>

      {/* Full Preview Dialog */}
      <Dialog
        open={!!previewSlide}
        onOpenChange={(open) => !open && setPreviewSlide(null)}
      >
        <DialogContent className="max-w-4xl p-0 overflow-hidden">
          {previewSlide && (
            <div className="relative h-[350px] md:h-[450px]">
              <img
                src={previewSlide.image_url}
                alt={previewSlide.title}
                className="w-full h-full object-cover"
              />
              <div className="absolute inset-0 bg-gradient-to-br from-primary/85 via-primary/75 to-primary/65" />
              <div className="absolute inset-0 flex items-center justify-center">
                <div className="text-center text-primary-foreground space-y-4 px-4">
                  <Badge
                    variant="secondary"
                    className="bg-primary-foreground/15 text-primary-foreground border-primary-foreground/20"
                  >
                    {previewSlide.badge_text}
                  </Badge>
                  <h1 className="text-3xl md:text-5xl font-bold whitespace-pre-line leading-tight">
                    {previewSlide.title}
                  </h1>
                  <p className="text-lg opacity-90">{previewSlide.subtitle}</p>
                  <div className="flex gap-3 justify-center">
                    <Button size="lg" variant="secondary">
                      {previewSlide.cta_primary_text}{" "}
                      <ArrowRight className="ml-2 h-4 w-4" />
                    </Button>
                    <Button
                      size="lg"
                      variant="outline"
                      className="bg-transparent border-primary-foreground/30 text-primary-foreground"
                    >
                      {previewSlide.cta_secondary_text}
                    </Button>
                  </div>
                </div>
              </div>
            </div>
          )}
        </DialogContent>
      </Dialog>

      {/* API info */}
      <Card className="p-4">
        <h4 className="font-semibold text-sm mb-2">API Endpoints (for PHP backend)</h4>
        <div className="text-xs text-muted-foreground space-y-1 font-mono">
          <p>GET /api/home/slider — Fetch all active slides (public)</p>
          <p>POST /api/home/slider — Create new slide (admin)</p>
          <p>PUT /api/home/slider/&#123;id&#125; — Update slide (admin)</p>
          <p>DELETE /api/home/slider/&#123;id&#125; — Delete slide (admin)</p>
        </div>
      </Card>
    </div>
  );
}
