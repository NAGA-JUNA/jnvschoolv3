import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { ExternalLink, Copy, Check, MessageCircle } from "lucide-react";
import { useToast } from "@/hooks/use-toast";

const mockGroups = [
  { id: 1, name: "Parents Group - Class 10", link: "https://chat.whatsapp.com/abc123" },
  { id: 2, name: "Teachers Group", link: "https://chat.whatsapp.com/def456" },
  { id: 3, name: "School Announcements", link: "https://chat.whatsapp.com/ghi789" },
];

const mockShareItems = [
  { type: "notification", id: 1, title: "Annual Day Registration" },
  { type: "notification", id: 2, title: "Summer Break Notice" },
  { type: "event", id: 1, title: "Parent-Teacher Meeting" },
  { type: "event", id: 2, title: "Annual Day" },
];

export default function WhatsAppManualPage() {
  const { toast } = useToast();
  const [selectedItem, setSelectedItem] = useState("");
  const [copied, setCopied] = useState(false);

  const item = mockShareItems.find((i) => `${i.type}-${i.id}` === selectedItem);
  const message = item
    ? `📢 *${item.title}*\n\nDear Parents/Students,\n\nPlease check the latest update from our school regarding "${item.title}".\n\nVisit our website for details.\n\n— SchoolAdmin`
    : "";

  const handleCopy = () => {
    navigator.clipboard.writeText(message);
    setCopied(true);
    toast({ title: "Copied!", description: "Message copied to clipboard" });
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="space-y-6">
      <PageHeader title="WhatsApp Sharing" description="Manually share notifications and events via WhatsApp" />

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Compose message */}
        <Card className="p-5 space-y-4">
          <h3 className="font-semibold">Compose Message</h3>
          <div>
            <Label>Select Item to Share</Label>
            <Select value={selectedItem} onValueChange={setSelectedItem}>
              <SelectTrigger><SelectValue placeholder="Choose notification or event" /></SelectTrigger>
              <SelectContent>
                {mockShareItems.map((i) => (
                  <SelectItem key={`${i.type}-${i.id}`} value={`${i.type}-${i.id}`}>
                    [{i.type}] {i.title}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {message && (
            <>
              <div>
                <Label>Generated Message</Label>
                <Textarea value={message} readOnly rows={8} className="mt-1 font-mono text-xs" />
              </div>
              <div className="flex gap-2">
                <Button onClick={handleCopy} variant="outline" className="gap-2">
                  {copied ? <Check className="h-4 w-4" /> : <Copy className="h-4 w-4" />}
                  {copied ? "Copied!" : "Copy to Clipboard"}
                </Button>
                <Button onClick={() => window.open("https://web.whatsapp.com", "_blank")} className="gap-2">
                  <MessageCircle className="h-4 w-4" /> Open WhatsApp Web
                </Button>
              </div>
              <Button variant="secondary" className="w-full">Mark as Shared on WhatsApp</Button>
            </>
          )}
        </Card>

        {/* Saved groups */}
        <Card className="p-5 space-y-4">
          <h3 className="font-semibold">Saved WhatsApp Groups</h3>
          <div className="space-y-2">
            {mockGroups.map((g) => (
              <div key={g.id} className="flex items-center justify-between p-3 rounded-lg border bg-muted/30">
                <div>
                  <p className="font-medium text-sm">{g.name}</p>
                  <p className="text-xs text-muted-foreground truncate max-w-[200px]">{g.link}</p>
                </div>
                <Button variant="ghost" size="sm" onClick={() => window.open(g.link, "_blank")}>
                  <ExternalLink className="h-4 w-4" />
                </Button>
              </div>
            ))}
          </div>
        </Card>
      </div>

      {/* Share logs */}
      <Card>
        <div className="p-4 border-b"><h3 className="font-semibold">Share History</h3></div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Item</TableHead>
              <TableHead>Type</TableHead>
              <TableHead>Shared At</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow>
              <TableCell className="font-medium">Annual Day Registration</TableCell>
              <TableCell>Notification</TableCell>
              <TableCell>2024-03-01 14:30</TableCell>
            </TableRow>
            <TableRow>
              <TableCell className="font-medium">Parent-Teacher Meeting</TableCell>
              <TableCell>Event</TableCell>
              <TableCell>2024-02-28 10:15</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </Card>
    </div>
  );
}
