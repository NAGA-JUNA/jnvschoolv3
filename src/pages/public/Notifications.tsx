import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Bell, Paperclip, AlertTriangle, Search } from "lucide-react";
import { useState } from "react";
import { cn } from "@/lib/utils";
import { mockPublicNotifications } from "@/data/mockSchoolData";

const urgencyColors = {
  normal: "bg-secondary text-secondary-foreground",
  important: "bg-warning/10 text-warning border-warning/20",
  urgent: "bg-destructive/10 text-destructive border-destructive/20",
};

export default function PublicNotifications() {
  const [selected, setSelected] = useState<typeof mockPublicNotifications[0] | null>(null);
  const [search, setSearch] = useState("");
  const [filter, setFilter] = useState<"all" | "normal" | "important" | "urgent">("all");

  const filtered = mockPublicNotifications.filter((n) => {
    const matchSearch = n.title.toLowerCase().includes(search.toLowerCase()) || n.body.toLowerCase().includes(search.toLowerCase());
    const matchFilter = filter === "all" || n.urgency === filter;
    return matchSearch && matchFilter;
  });

  return (
    <div className="max-w-4xl mx-auto py-10 px-4 space-y-6">
      <div className="text-center space-y-2">
        <h1 className="text-3xl font-bold">School Notifications</h1>
        <p className="text-muted-foreground">Stay updated with the latest announcements</p>
      </div>

      {/* Search & Filter */}
      <div className="flex flex-col sm:flex-row gap-3">
        <div className="relative flex-1">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input placeholder="Search notifications..." value={search} onChange={(e) => setSearch(e.target.value)} className="pl-9" />
        </div>
        <div className="flex gap-2">
          {(["all", "normal", "important", "urgent"] as const).map((f) => (
            <Button key={f} variant={filter === f ? "default" : "outline"} size="sm" onClick={() => setFilter(f)} className="capitalize">
              {f}
            </Button>
          ))}
        </div>
      </div>

      <div className="space-y-4">
        {filtered.map((n) => (
          <Card key={n.id} className="p-5 hover:shadow-md transition-shadow cursor-pointer" onClick={() => setSelected(n)}>
            <div className="flex items-start gap-4">
              <div className={cn("p-2 rounded-lg flex-shrink-0", n.urgency === "urgent" ? "bg-destructive/10" : "bg-primary/10")}>
                {n.urgency === "urgent" ? <AlertTriangle className="h-5 w-5 text-destructive" /> : <Bell className="h-5 w-5 text-primary" />}
              </div>
              <div className="flex-1 min-w-0">
                <div className="flex items-center gap-2 mb-1 flex-wrap">
                  <h3 className="font-semibold">{n.title}</h3>
                  <Badge variant="outline" className={cn("text-xs capitalize", urgencyColors[n.urgency])}>{n.urgency}</Badge>
                </div>
                <p className="text-sm text-muted-foreground line-clamp-2">{n.body}</p>
                <div className="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                  <span>Posted: {n.created_at}</span>
                  <span>Valid till: {n.expiry}</span>
                  {n.attachment_url && <span className="flex items-center gap-1"><Paperclip className="h-3 w-3" /> Attachment</span>}
                </div>
              </div>
            </div>
          </Card>
        ))}
        {filtered.length === 0 && (
          <div className="text-center py-16 text-muted-foreground">
            <Bell className="h-12 w-12 mx-auto mb-3 opacity-30" />
            <p>No notifications found matching your criteria.</p>
          </div>
        )}
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
                <p>Valid till: {selected.expiry}</p>
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
