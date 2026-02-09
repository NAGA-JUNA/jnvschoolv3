import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Dialog, DialogContent } from "@/components/ui/dialog";
import { Image, Video, X } from "lucide-react";
import { galleryCategories } from "@/data/mockSchoolData";

export default function PublicGallery() {
  const [selectedImage, setSelectedImage] = useState<string | null>(null);
  const [activeCategory, setActiveCategory] = useState<string>("all");

  const filteredCategories = activeCategory === "all"
    ? galleryCategories
    : galleryCategories.filter((c) => c.slug === activeCategory);

  return (
    <div className="max-w-6xl mx-auto py-10 px-4 space-y-8">
      <div className="text-center space-y-2">
        <h1 className="text-3xl font-bold">School Gallery</h1>
        <p className="text-muted-foreground">Photos and videos from school life and events</p>
      </div>

      {/* Category Filter */}
      <div className="flex flex-wrap gap-2 justify-center">
        <Button
          variant={activeCategory === "all" ? "default" : "outline"}
          size="sm"
          onClick={() => setActiveCategory("all")}
        >
          All Categories
        </Button>
        {galleryCategories.map((cat) => (
          <Button
            key={cat.slug}
            variant={activeCategory === cat.slug ? "default" : "outline"}
            size="sm"
            onClick={() => setActiveCategory(cat.slug)}
            className="gap-1.5"
          >
            {cat.type === "images" ? <Image className="h-3.5 w-3.5" /> : <Video className="h-3.5 w-3.5" />}
            {cat.name}
            <Badge variant="secondary" className="text-xs ml-1 px-1.5">{cat.items.length}</Badge>
          </Button>
        ))}
      </div>

      {/* Gallery Grid */}
      {filteredCategories.map((cat) => (
        <div key={cat.slug} className="space-y-4">
          {activeCategory === "all" && (
            <h2 className="text-xl font-bold flex items-center gap-2">
              {cat.type === "images" ? <Image className="h-5 w-5 text-primary" /> : <Video className="h-5 w-5 text-primary" />}
              {cat.name}
              <Badge variant="outline" className="text-xs">{cat.items.length} items</Badge>
            </h2>
          )}

          {cat.type === "images" ? (
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              {cat.items.map((item) => (
                <Card
                  key={item.id}
                  className="overflow-hidden cursor-pointer group"
                  onClick={() => setSelectedImage(item.url)}
                >
                  <div className="aspect-square relative">
                    <img src={item.url} alt={item.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" />
                    <div className="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors" />
                  </div>
                  <div className="p-3">
                    <p className="text-sm font-medium truncate">{item.title}</p>
                  </div>
                </Card>
              ))}
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {cat.items.map((item) => (
                <Card key={item.id} className="overflow-hidden">
                  <div className="aspect-video">
                    <iframe
                      src={`https://www.youtube.com/embed/${item.url}`}
                      title={item.title}
                      className="w-full h-full"
                      allowFullScreen
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    />
                  </div>
                  <div className="p-4">
                    <p className="font-medium">{item.title}</p>
                  </div>
                </Card>
              ))}
            </div>
          )}
        </div>
      ))}

      {/* Lightbox */}
      <Dialog open={!!selectedImage} onOpenChange={() => setSelectedImage(null)}>
        <DialogContent className="max-w-4xl p-0 bg-black border-none">
          <button onClick={() => setSelectedImage(null)} className="absolute top-4 right-4 text-white z-10 hover:opacity-70">
            <X className="h-6 w-6" />
          </button>
          {selectedImage && (
            <img src={selectedImage.replace("w=400", "w=1200")} alt="Gallery" className="w-full h-auto max-h-[80vh] object-contain" />
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}
