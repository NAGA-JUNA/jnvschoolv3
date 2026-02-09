import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Send, Paperclip } from "lucide-react";
import { useToast } from "@/hooks/use-toast";

export default function PostNotification() {
  const { toast } = useToast();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    toast({ title: "Submitted!", description: "Your notification has been sent for approval." });
  };

  return (
    <div className="space-y-6">
      <PageHeader title="Post Notification" description="Submit a notification for admin approval" />

      <Card className="max-w-2xl p-6">
        <form onSubmit={handleSubmit} className="space-y-5">
          <div><Label>Title</Label><Input placeholder="Notification title" required /></div>
          <div><Label>Body</Label><Textarea placeholder="Write your notification content..." rows={5} required /></div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>Urgency</Label>
              <Select defaultValue="normal">
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="normal">Normal</SelectItem>
                  <SelectItem value="important">Important</SelectItem>
                  <SelectItem value="urgent">Urgent</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div><Label>Expiry Date</Label><Input type="date" /></div>
          </div>
          <div>
            <Label>Attachment (PDF/JPG/PNG, max 5MB)</Label>
            <Input type="file" accept=".pdf,.jpg,.jpeg,.png" className="mt-1" />
          </div>
          <div className="flex justify-end gap-2">
            <Button type="submit"><Send className="h-4 w-4 mr-2" /> Submit for Approval</Button>
          </div>
        </form>
      </Card>
    </div>
  );
}
