import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Switch } from "@/components/ui/switch";
import { Save } from "lucide-react";

export default function SettingsPage() {
  return (
    <div className="space-y-6">
      <PageHeader title="Settings" description="Configure system settings" />

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card className="p-5 space-y-4">
          <h3 className="font-semibold">School Information</h3>
          <div><Label>School Name</Label><Input defaultValue="Springfield International School" /></div>
          <div><Label>School Email</Label><Input defaultValue="info@school.com" /></div>
          <div><Label>Phone Number</Label><Input defaultValue="+91 9876543200" /></div>
          <div><Label>Address</Label><Textarea defaultValue="123 Education Lane, Knowledge City" rows={2} /></div>
          <Button><Save className="h-4 w-4 mr-2" /> Save</Button>
        </Card>

        <Card className="p-5 space-y-4">
          <h3 className="font-semibold">System Alert Banner</h3>
          <div><Label>Alert Message</Label><Textarea placeholder="Type alert message..." rows={3} /></div>
          <div className="flex items-center gap-3">
            <Switch id="alert-active" />
            <Label htmlFor="alert-active">Show alert on dashboard</Label>
          </div>
          <Button><Save className="h-4 w-4 mr-2" /> Save Alert</Button>
        </Card>

        <Card className="p-5 space-y-4">
          <h3 className="font-semibold">WhatsApp Groups</h3>
          <div><Label>Group Name</Label><Input placeholder="Group name" /></div>
          <div><Label>Group Link</Label><Input placeholder="https://chat.whatsapp.com/..." /></div>
          <Button variant="outline">Add Group</Button>
        </Card>

        <Card className="p-5 space-y-4">
          <h3 className="font-semibold">SMTP Settings</h3>
          <div><Label>SMTP Host</Label><Input placeholder="smtp.school.com" /></div>
          <div className="grid grid-cols-2 gap-4">
            <div><Label>SMTP Port</Label><Input placeholder="587" /></div>
            <div><Label>Encryption</Label><Input placeholder="TLS" /></div>
          </div>
          <div><Label>Username</Label><Input placeholder="noreply@school.com" /></div>
          <div><Label>Password</Label><Input type="password" placeholder="••••••••" /></div>
          <Button><Save className="h-4 w-4 mr-2" /> Save SMTP</Button>
        </Card>
      </div>
    </div>
  );
}
