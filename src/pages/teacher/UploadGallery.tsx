import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Upload, Link as LinkIcon } from "lucide-react";
import { useToast } from "@/hooks/use-toast";

const mockCategories = [
  { id: 1, name: "Annual Day 2024", type: "images" },
  { id: 2, name: "Sports Day", type: "images" },
  { id: 3, name: "School Videos", type: "videos" },
];

export default function UploadGallery() {
  const { toast } = useToast();

  return (
    <div className="space-y-6">
      <PageHeader title="Upload Gallery" description="Upload images or add YouTube links" />

      <Card className="max-w-2xl p-6">
        <Tabs defaultValue="images">
          <TabsList className="mb-4">
            <TabsTrigger value="images">Upload Images</TabsTrigger>
            <TabsTrigger value="youtube">Add YouTube Link</TabsTrigger>
          </TabsList>

          <TabsContent value="images" className="space-y-4">
            <div><Label>Category</Label>
              <Select>
                <SelectTrigger><SelectValue placeholder="Select image category" /></SelectTrigger>
                <SelectContent>
                  {mockCategories.filter((c) => c.type === "images").map((c) => (
                    <SelectItem key={c.id} value={c.id.toString()}>{c.name}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div><Label>Title</Label><Input placeholder="Photo title" /></div>
            <div>
              <Label>Image Files</Label>
              <Input type="file" accept="image/*" multiple className="mt-1" />
            </div>
            <Button onClick={() => toast({ title: "Uploaded!", description: "Images submitted for approval." })}>
              <Upload className="h-4 w-4 mr-2" /> Upload Images
            </Button>
          </TabsContent>

          <TabsContent value="youtube" className="space-y-4">
            <div><Label>Videos Category</Label>
              <Select>
                <SelectTrigger><SelectValue placeholder="Select videos category" /></SelectTrigger>
                <SelectContent>
                  {mockCategories.filter((c) => c.type === "videos").map((c) => (
                    <SelectItem key={c.id} value={c.id.toString()}>{c.name}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div><Label>Title</Label><Input placeholder="Video title" /></div>
            <div><Label>YouTube URL</Label><Input placeholder="https://youtube.com/watch?v=..." /></div>
            <Button onClick={() => toast({ title: "Submitted!", description: "YouTube link submitted for approval." })}>
              <LinkIcon className="h-4 w-4 mr-2" /> Submit Link
            </Button>
          </TabsContent>
        </Tabs>
      </Card>
    </div>
  );
}
