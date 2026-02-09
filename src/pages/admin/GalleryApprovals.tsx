import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { CheckCircle, XCircle, Eye, Image, Video } from "lucide-react";
import { GalleryItem } from "@/types";
import { Badge } from "@/components/ui/badge";

const mockItems: GalleryItem[] = [
  { id: 1, category_id: 1, category_name: "Annual Day 2024", type: "image", url: "/uploads/photo1.jpg", title: "Stage Decoration", status: "pending", submitted_by: 1, submitted_by_name: "Priya Singh", created_at: "2024-03-01" },
  { id: 2, category_id: 3, category_name: "School Videos", type: "youtube", url: "https://youtube.com/watch?v=abc123", title: "Annual Day Highlights", status: "pending", submitted_by: 2, submitted_by_name: "Rajesh Kumar", created_at: "2024-03-02" },
  { id: 3, category_id: 2, category_name: "Sports Day", type: "image", url: "/uploads/photo2.jpg", title: "100m Sprint", status: "approved", submitted_by: 1, submitted_by_name: "Priya Singh", created_at: "2024-02-28" },
];

export default function GalleryApprovalsPage() {
  return (
    <div className="space-y-6">
      <PageHeader title="Gallery Approvals" description="Review uploaded images and YouTube links" />

      <Card>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Title</TableHead>
              <TableHead>Type</TableHead>
              <TableHead>Category</TableHead>
              <TableHead>Submitted By</TableHead>
              <TableHead>Status</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {mockItems.map((item) => (
              <TableRow key={item.id}>
                <TableCell className="font-medium">{item.title}</TableCell>
                <TableCell>
                  <Badge variant="outline" className="gap-1">
                    {item.type === "image" ? <Image className="h-3 w-3" /> : <Video className="h-3 w-3" />}
                    {item.type}
                  </Badge>
                </TableCell>
                <TableCell>{item.category_name}</TableCell>
                <TableCell>{item.submitted_by_name}</TableCell>
                <TableCell><StatusBadge status={item.status} /></TableCell>
                <TableCell className="text-right space-x-1">
                  <Button variant="ghost" size="icon"><Eye className="h-4 w-4" /></Button>
                  {item.status === "pending" && (
                    <>
                      <Button variant="ghost" size="icon"><CheckCircle className="h-4 w-4 text-success" /></Button>
                      <Button variant="ghost" size="icon"><XCircle className="h-4 w-4 text-destructive" /></Button>
                    </>
                  )}
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Card>
    </div>
  );
}
