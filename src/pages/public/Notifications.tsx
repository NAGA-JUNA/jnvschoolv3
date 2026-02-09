import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Bell, Paperclip, AlertTriangle } from "lucide-react";
import { useState } from "react";
import { cn } from "@/lib/utils";

const urgencyColors = {
  normal: "bg-secondary text-secondary-foreground",
  important: "bg-warning/10 text-warning border-warning/20",
  urgent: "bg-destructive/10 text-destructive border-destructive/20",
};

const mockNotifications = [
  { id: 1, title: "Annual Day Registration Open", body: "All students must register for the annual day events by March 15th. Forms are available at the front office or can be downloaded from the school website.", urgency: "important" as const, expiry: "2024-03-15", attachment_url: "/notice.pdf", created_at: "2024-03-01" },
  { id: 2, title: "Summer Break Schedule", body: "School will remain closed from April 15 to June 1 for summer vacations. New session begins June 2.", urgency: "normal" as const, expiry: "2024-06-01", created_at: "2024-02-28" },
  { id: 3, title: "Emergency: Water Supply Disruption", body: "Due to maintenance work, water supply will be interrupted tomorrow. Students are advised to carry water bottles.", urgency: "urgent" as const, expiry: "2024-03-05", created_at: "2024-03-04" },
];

export default function PublicNotifications() {
  const [selected, setSelected] = useState<typeof mockNotifications[0] | null>(null);

  return (
    <div className="max-w-4xl mx-auto py-10 px-4 space-y-6">
      <div className="text-center space-y-2">
        <h1 className="text-3xl font-bold">School Notifications</h1>
        <p className="text-muted-foreground">Stay updated with the latest announcements</p>
      </div>

      <div className="space-y-4">
        {mockNotifications.map((n) => (
          <Card
            key={n.id}
            className="p-5 hover:shadow-md transition-shadow cursor-pointer"
            onClick={() => setSelected(n)}
          >
            <div className="flex items-start gap-4">
              <div className={cn("p-2 rounded-lg flex-shrink-0", n.urgency === "urgent" ? "bg-destructive/10" : "bg-primary/10")}>
                {n.urgency === "urgent" ? <AlertTriangle className="h-5 w-5 text-destructive" /> : <Bell className="h-5 w-5 text-primary" />}
              </div>
              <div className="flex-1 min-w-0">
                <div className="flex items-center gap-2 mb-1">
                  <h3 className="font-semibold">{n.title}</h3>
                  <Badge variant="outline" className={cn("text-xs capitalize", urgencyColors[n.urgency])}>{n.urgency}</Badge>
                </div>
                <p className="text-sm text-muted-foreground line-clamp-2">{n.body}</p>
                <div className="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                  <span>Posted: {n.created_at}</span>
                  <span>Expires: {n.expiry}</span>
                  {n.attachment_url && <span className="flex items-center gap-1"><Paperclip className="h-3 w-3" /> Attachment</span>}
                </div>
              </div>
            </div>
          </Card>
        ))}
      </div>

      <Dialog open={!!selected} onOpenChange={() => setSelected(null)}>
        <DialogContent>
          <DialogHeader><DialogTitle>{selected?.title}</DialogTitle></DialogHeader>
          {selected && (
            <div className="space-y-3">
              <Badge variant="outline" className={cn("capitalize", urgencyColors[selected.urgency])}>{selected.urgency}</Badge>
              <p className="text-sm leading-relaxed">{selected.body}</p>
              <div className="text-xs text-muted-foreground space-y-1">
                <p>Posted: {selected.created_at}</p>
                <p>Expires: {selected.expiry}</p>
              </div>
              {selected.attachment_url && (
                <Button variant="outline" size="sm"><Paperclip className="h-4 w-4 mr-2" /> Download Attachment</Button>
              )}
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}
