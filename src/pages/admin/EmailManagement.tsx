import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Plus, Copy, ExternalLink } from "lucide-react";
import { OfficialEmail } from "@/types";
import { useToast } from "@/hooks/use-toast";

const mockEmails: OfficialEmail[] = [
  { id: 1, user_id: 1, user_name: "Priya Singh", email: "priya.singh@school.com", status: "active", created_at: "2024-01-15" },
  { id: 2, user_id: 2, user_name: "Rajesh Kumar", email: "rajesh.kumar@school.com", status: "active", created_at: "2024-02-10" },
];

export default function EmailManagementPage() {
  const { toast } = useToast();
  const [dialogOpen, setDialogOpen] = useState(false);
  const [generatedPassword, setGeneratedPassword] = useState("");

  const handleCreate = () => {
    setGeneratedPassword("Temp@" + Math.random().toString(36).slice(2, 10));
  };

  const copyPassword = () => {
    navigator.clipboard.writeText(generatedPassword);
    toast({ title: "Copied!", description: "Password copied — share securely." });
  };

  return (
    <div className="space-y-6">
      <PageHeader
        title="Email Management"
        description="Create and manage official email accounts for staff"
        action={
          <Dialog open={dialogOpen} onOpenChange={(o) => { setDialogOpen(o); if (!o) setGeneratedPassword(""); }}>
            <DialogTrigger asChild>
              <Button><Plus className="h-4 w-4 mr-2" /> Create Email</Button>
            </DialogTrigger>
            <DialogContent>
              <DialogHeader><DialogTitle>Create Official Email</DialogTitle></DialogHeader>
              <form className="space-y-4" onSubmit={(e) => { e.preventDefault(); handleCreate(); }}>
                <div><Label>Staff Member</Label><Input placeholder="Staff name" /></div>
                <div><Label>Username (auto-generated)</Label><Input placeholder="firstname.lastname" /></div>
                {generatedPassword && (
                  <Card className="p-4 bg-muted/50 space-y-2">
                    <p className="text-sm font-medium">✅ Email created successfully!</p>
                    <div className="flex items-center gap-2">
                      <Input value={generatedPassword} readOnly className="font-mono" />
                      <Button type="button" variant="outline" size="icon" onClick={copyPassword}>
                        <Copy className="h-4 w-4" />
                      </Button>
                    </div>
                    <p className="text-xs text-destructive font-medium">⚠️ This password is shown only once. Copy it now.</p>
                  </Card>
                )}
                <div className="flex justify-end gap-2">
                  <Button variant="outline" type="button" onClick={() => setDialogOpen(false)}>Close</Button>
                  {!generatedPassword && <Button type="submit">Generate Account</Button>}
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
              <TableHead>Staff Member</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Created</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {mockEmails.map((em) => (
              <TableRow key={em.id}>
                <TableCell className="font-medium">{em.user_name}</TableCell>
                <TableCell>{em.email}</TableCell>
                <TableCell><StatusBadge status={em.status} /></TableCell>
                <TableCell>{em.created_at}</TableCell>
                <TableCell className="text-right">
                  <Button variant="ghost" size="sm" onClick={() => window.open("https://webmail.school.com", "_blank")}>
                    <ExternalLink className="h-4 w-4 mr-1" /> Webmail
                  </Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Card>
    </div>
  );
}
