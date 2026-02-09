import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Badge } from "@/components/ui/badge";
import { Plus, Pencil, Trash2, MapPin } from "lucide-react";
import { SchoolEvent } from "@/types";

const eventTypeColors: Record<string, string> = {
  academic: "bg-kpi-blue/10 text-kpi-blue",
  cultural: "bg-kpi-purple/10 text-kpi-purple",
  sports: "bg-kpi-green/10 text-kpi-green",
  holiday: "bg-kpi-orange/10 text-kpi-orange",
  other: "bg-secondary text-secondary-foreground",
};

const mockEvents: SchoolEvent[] = [
  { id: 1, title: "Parent-Teacher Meeting", description: "Quarterly PTM for all classes", date: "2024-03-20", location: "School Auditorium", type: "academic", created_at: "2024-03-01" },
  { id: 2, title: "Annual Day", description: "Annual day celebration with cultural programs", date: "2024-04-10", end_date: "2024-04-11", location: "Main Ground", type: "cultural", created_at: "2024-02-15" },
  { id: 3, title: "Sports Day", description: "Inter-house sports competition", date: "2024-03-25", location: "Sports Complex", type: "sports", created_at: "2024-02-20" },
  { id: 4, title: "Holi Holiday", description: "School closed for Holi", date: "2024-03-25", type: "holiday", created_at: "2024-03-10" },
];

export default function EventsPage() {
  const [dialogOpen, setDialogOpen] = useState(false);

  return (
    <div className="space-y-6">
      <PageHeader
        title="Events Management"
        description="Manage school events and calendar"
        action={
          <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
            <DialogTrigger asChild>
              <Button><Plus className="h-4 w-4 mr-2" /> Add Event</Button>
            </DialogTrigger>
            <DialogContent className="max-w-lg">
              <DialogHeader><DialogTitle>Add Event</DialogTitle></DialogHeader>
              <form className="space-y-4">
                <div><Label>Title</Label><Input placeholder="Event title" /></div>
                <div><Label>Description</Label><Textarea placeholder="Event description" rows={3} /></div>
                <div className="grid grid-cols-2 gap-4">
                  <div><Label>Start Date</Label><Input type="date" /></div>
                  <div><Label>End Date (optional)</Label><Input type="date" /></div>
                  <div><Label>Location</Label><Input placeholder="Location" /></div>
                  <div><Label>Type</Label>
                    <Select>
                      <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                      <SelectContent>
                        <SelectItem value="academic">Academic</SelectItem>
                        <SelectItem value="cultural">Cultural</SelectItem>
                        <SelectItem value="sports">Sports</SelectItem>
                        <SelectItem value="holiday">Holiday</SelectItem>
                        <SelectItem value="other">Other</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </div>
                <div className="flex justify-end gap-2">
                  <Button variant="outline" type="button" onClick={() => setDialogOpen(false)}>Cancel</Button>
                  <Button type="submit">Create Event</Button>
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
              <TableHead>Event</TableHead>
              <TableHead>Type</TableHead>
              <TableHead>Date</TableHead>
              <TableHead>Location</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {mockEvents.map((e) => (
              <TableRow key={e.id}>
                <TableCell>
                  <div><span className="font-medium">{e.title}</span><p className="text-xs text-muted-foreground mt-0.5">{e.description}</p></div>
                </TableCell>
                <TableCell>
                  <Badge variant="outline" className={`capitalize text-xs ${eventTypeColors[e.type]}`}>{e.type}</Badge>
                </TableCell>
                <TableCell>{e.date}{e.end_date ? ` — ${e.end_date}` : ""}</TableCell>
                <TableCell>{e.location ? <span className="flex items-center gap-1"><MapPin className="h-3 w-3" />{e.location}</span> : "—"}</TableCell>
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
