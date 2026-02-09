import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent } from "@/components/ui/dialog";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Image, Video, X } from "lucide-react";

const mockCategories = [
  {
    id: 1, name: "Annual Day 2024", slug: "annual-day", type: "images" as const,
    items: [
      { id: 1, url: "https://images.unsplash.com/photo-1523050854058-8df90110c7f1?w=400", title: "Stage Decoration" },
      { id: 2, url: "https://images.unsplash.com/photo-1577896851231-70ef18881754?w=400", title: "Student Performance" },
      { id: 3, url: "https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400", title: "Group Photo" },
      { id: 4, url: "https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=400", title: "Award Ceremony" },
    ],
  },
  {
    id: 2, name: "Sports Day", slug: "sports-day", type: "images" as const,
    items: [
      { id: 5, url: "https://images.unsplash.com/photo-1461896836934-bd45ba8fcf9b?w=400", title: "100m Sprint" },
      { id: 6, url: "https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=400", title: "Long Jump" },
    ],
  },
  {
    id: 3, name: "School Videos", slug: "videos", type: "videos" as const,
    items: [
      { id: 7, url: "dQw4w9WgXcQ", title: "Annual Day Highlights 2024" },
      { id: 8, url: "dQw4w9WgXcQ", title: "Sports Day Montage" },
    ],
  },
];

export default function PublicGallery() {
  const [selectedImage, setSelectedImage] = useState<string | null>(null);

  return (
    <div className="max-w-6xl mx-auto py-10 px-4 space-y-8">
      <div className="text-center space-y-2">
        <h1 className="text-3xl font-bold">School Gallery</h1>
        <p className="text-muted-foreground">Photos and videos from school events</p>
      </div>

      <Tabs defaultValue={mockCategories[0].slug}>
        <TabsList className="flex flex-wrap h-auto gap-1 mb-6">
          {mockCategories.map((cat) => (
            <TabsTrigger key={cat.slug} value={cat.slug} className="gap-2">
              {cat.type === "images" ? <Image className="h-3.5 w-3.5" /> : <Video className="h-3.5 w-3.5" />}
              {cat.name}
            </TabsTrigger>
          ))}
        </TabsList>

        {mockCategories.map((cat) => (
          <TabsContent key={cat.slug} value={cat.slug}>
            {cat.type === "images" ? (
              <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {cat.items.map((item) => (
                  <Card
                    key={item.id}
                    className="overflow-hidden cursor-pointer group"
                    onClick={() => setSelectedImage(item.url)}
                  >
                    <div className="aspect-square relative">
                      <img src={item.url} alt={item.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
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
          </TabsContent>
        ))}
      </Tabs>

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
