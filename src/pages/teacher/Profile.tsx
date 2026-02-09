import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Save } from "lucide-react";
import { useToast } from "@/hooks/use-toast";

export default function TeacherProfile() {
  const { toast } = useToast();

  return (
    <div className="space-y-6">
      <PageHeader title="My Profile" description="Manage your profile information" />

      <Card className="max-w-lg p-6">
        <form
          onSubmit={(e) => { e.preventDefault(); toast({ title: "Saved!", description: "Profile updated." }); }}
          className="space-y-4"
        >
          <div><Label>Full Name</Label><Input defaultValue="Priya Singh" /></div>
          <div><Label>Email</Label><Input defaultValue="priya@school.com" readOnly className="bg-muted" /></div>
          <div><Label>Phone</Label><Input defaultValue="9876543210" /></div>
          <div><Label>WhatsApp Number</Label><Input defaultValue="9876543210" /></div>
          <div><Label>Subject</Label><Input defaultValue="Mathematics" /></div>
          <Button type="submit"><Save className="h-4 w-4 mr-2" /> Save Profile</Button>
        </form>
      </Card>
    </div>
  );
}
