import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Checkbox } from "@/components/ui/checkbox";
import { Search, CheckCircle, XCircle, Paperclip, Eye } from "lucide-react";
import { Notification, UrgencyLevel } from "@/types";
import { Badge } from "@/components/ui/badge";
import { cn } from "@/lib/utils";

const urgencyColors: Record<UrgencyLevel, string> = {
  normal: "bg-secondary text-secondary-foreground",
  important: "bg-warning/10 text-warning border-warning/20",
  urgent: "bg-destructive/10 text-destructive border-destructive/20",
};

const mockNotifications: Notification[] = [
  { id: 1, title: "Annual Day Registration", body: "All students must register for annual day events by March 15th.", urgency: "important", expiry: "2024-03-15", attachment_url: "/sample.pdf", attachment_type: "pdf", status: "pending", submitted_by: 1, submitted_by_name: "Priya Singh", created_at: "2024-03-01" },
  { id: 2, title: "Summer Break Notice", body: "School will remain closed from April 15 to June 1.", urgency: "normal", expiry: "2024-06-01", status: "approved", submitted_by: 2, submitted_by_name: "Rajesh Kumar", created_at: "2024-02-28" },
  { id: 3, title: "Emergency: Water Supply Issue", body: "Due to maintenance, water supply will be interrupted tomorrow.", urgency: "urgent", expiry: "2024-03-05", status: "pending", submitted_by: 1, submitted_by_name: "Priya Singh", created_at: "2024-03-04" },
];

export default function NotificationsPage() {
  const [search, setSearch] = useState("");
  const [selected, setSelected] = useState<number[]>([]);
  const [rejectDialog, setRejectDialog] = useState<Notification | null>(null);
  const [viewDialog, setViewDialog] = useState<Notification | null>(null);
  const [rejectionReason, setRejectionReason] = useState("");

  const filtered = mockNotifications.filter((n) =>
    n.title.toLowerCase().includes(search.toLowerCase())
  );

  const toggleSelect = (id: number) => {
    setSelected((s) => s.includes(id) ? s.filter((i) => i !== id) : [...s, id]);
  };

  const pendingIds = filtered.filter((n) => n.status === "pending").map((n) => n.id);
  const allPendingSelected = pendingIds.length > 0 && pendingIds.every((id) => selected.includes(id));

  return (
    <div className="space-y-6">
      <PageHeader
        title="Notifications Approval"
        description="Review and approve teacher notifications"
        action={
          selected.length > 0 ? (
            <Button onClick={() => setSelected([])}><CheckCircle className="h-4 w-4 mr-2" /> Bulk Approve ({selected.length})</Button>
          ) : undefined
        }
      />

      <Card>
        <div className="p-4 border-b">
          <div className="relative max-w-sm">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Search notifications..." value={search} onChange={(e) => setSearch(e.target.value)} className="pl-9" />
          </div>
        </div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead className="w-10">
                <Checkbox
                  checked={allPendingSelected}
                  onCheckedChange={() => {
                    setSelected(allPendingSelected ? [] : pendingIds);
                  }}
                />
              </TableHead>
              <TableHead>Title</TableHead>
              <TableHead>Urgency</TableHead>
              <TableHead>Submitted By</TableHead>
              <TableHead>Expiry</TableHead>
              <TableHead>Status</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filtered.map((n) => (
              <TableRow key={n.id}>
                <TableCell>
                  {n.status === "pending" && (
                    <Checkbox checked={selected.includes(n.id)} onCheckedChange={() => toggleSelect(n.id)} />
                  )}
                </TableCell>
                <TableCell className="font-medium">
                  <div className="flex items-center gap-2">
                    {n.title}
                    {n.attachment_url && <Paperclip className="h-3.5 w-3.5 text-muted-foreground" />}
                  </div>
                </TableCell>
                <TableCell>
                  <Badge variant="outline" className={cn("capitalize text-xs", urgencyColors[n.urgency])}>{n.urgency}</Badge>
                </TableCell>
                <TableCell>{n.submitted_by_name}</TableCell>
                <TableCell>{n.expiry}</TableCell>
                <TableCell><StatusBadge status={n.status} /></TableCell>
                <TableCell className="text-right space-x-1">
                  <Button variant="ghost" size="icon" onClick={() => setViewDialog(n)}><Eye className="h-4 w-4" /></Button>
                  {n.status === "pending" && (
                    <>
                      <Button variant="ghost" size="icon"><CheckCircle className="h-4 w-4 text-success" /></Button>
                      <Button variant="ghost" size="icon" onClick={() => { setRejectDialog(n); setRejectionReason(""); }}><XCircle className="h-4 w-4 text-destructive" /></Button>
                    </>
                  )}
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Card>

      {/* View Dialog */}
      <Dialog open={!!viewDialog} onOpenChange={() => setViewDialog(null)}>
        <DialogContent>
          <DialogHeader><DialogTitle>{viewDialog?.title}</DialogTitle></DialogHeader>
          {viewDialog && (
            <div className="space-y-3 text-sm">
              <p>{viewDialog.body}</p>
              <div className="flex items-center gap-4 text-muted-foreground">
                <span>Urgency: <Badge variant="outline" className={cn("capitalize text-xs ml-1", urgencyColors[viewDialog.urgency])}>{viewDialog.urgency}</Badge></span>
                <span>Expires: {viewDialog.expiry}</span>
              </div>
              {viewDialog.attachment_url && (
                <div className="flex items-center gap-2 text-primary">
                  <Paperclip className="h-4 w-4" />
                  <a href={viewDialog.attachment_url} className="underline">View Attachment</a>
                </div>
              )}
              <StatusBadge status={viewDialog.status} />
            </div>
          )}
        </DialogContent>
      </Dialog>

      {/* Reject Dialog */}
      <Dialog open={!!rejectDialog} onOpenChange={() => setRejectDialog(null)}>
        <DialogContent>
          <DialogHeader><DialogTitle>Reject: {rejectDialog?.title}</DialogTitle></DialogHeader>
          <div className="space-y-3">
            <Textarea placeholder="Reason for rejection..." value={rejectionReason} onChange={(e) => setRejectionReason(e.target.value)} rows={4} />
            <div className="flex justify-end gap-2">
              <Button variant="outline" onClick={() => setRejectDialog(null)}>Cancel</Button>
              <Button variant="destructive">Reject</Button>
            </div>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
