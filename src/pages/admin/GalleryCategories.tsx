import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Badge } from "@/components/ui/badge";
import { Plus, Pencil, Trash2, Image, Video } from "lucide-react";
import { GalleryCategory } from "@/types";

const mockCategories: GalleryCategory[] = [
  { id: 1, name: "Annual Day 2024", slug: "annual-day-2024", type: "images", item_count: 45, created_at: "2024-01-10" },
  { id: 2, name: "Sports Day", slug: "sports-day", type: "images", item_count: 32, created_at: "2024-02-05" },
  { id: 3, name: "School Videos", slug: "videos", type: "videos", item_count: 12, created_at: "2024-01-01" },
  { id: 4, name: "Science Fair", slug: "science-fair", type: "images", item_count: 28, created_at: "2024-03-01" },
];

export default function GalleryCategoriesPage() {
  const [dialogOpen, setDialogOpen] = useState(false);

  return (
    <div className="space-y-6">
      <PageHeader
        title="Gallery Categories"
        description="Manage gallery categories for images and videos"
        action={
          <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
            <DialogTrigger asChild>
              <Button><Plus className="h-4 w-4 mr-2" /> Add Category</Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader><DialogTitle>Add Category</DialogTitle></DialogHeader>
              <form className="space-y-4">
                <div><Label>Category Name</Label><Input placeholder="e.g. Annual Day 2024" /></div>
                <div><Label>Type</Label>
                  <Select>
                    <SelectTrigger><SelectValue placeholder="Select type" /></SelectTrigger>
                    <SelectContent>
                      <SelectItem value="images">Images</SelectItem>
                      <SelectItem value="videos">Videos (YouTube)</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                <div className="flex justify-end gap-2">
                  <Button variant="outline" type="button" onClick={() => setDialogOpen(false)}>Cancel</Button>
                  <Button type="submit">Create</Button>
                </div>
              </form>
            </DialogContent>
          </Dialog>
        }
      />

      <Card>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Category</TableHead>
              <TableHead>Type</TableHead>
              <TableHead>Items</TableHead>
              <TableHead>Created</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {mockCategories.map((c) => (
              <TableRow key={c.id}>
                <TableCell className="font-medium">{c.name}</TableCell>
                <TableCell>
                  <Badge variant="outline" className="gap-1">
                    {c.type === "images" ? <Image className="h-3 w-3" /> : <Video className="h-3 w-3" />}
                    {c.type}
                  </Badge>
                </TableCell>
                <TableCell>{c.item_count}</TableCell>
                <TableCell>{c.created_at}</TableCell>
                <TableCell className="text-right">
                  <Button variant="ghost" size="icon"><Pencil className="h-4 w-4" /></Button>
                  <Button variant="ghost" size="icon"><Trash2 className="h-4 w-4 text-destructive" /></Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Card>
    </div>
  );
}
