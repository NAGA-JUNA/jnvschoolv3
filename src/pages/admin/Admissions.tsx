import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Search, Download, Eye, CheckCircle, XCircle } from "lucide-react";
import { Admission } from "@/types";

const mockAdmissions: Admission[] = [
  { id: 1, student_name: "Aarav Gupta", class_applied: "6", parent_name: "Manoj Gupta", parent_phone: "9876543230", parent_email: "manoj@email.com", parent_whatsapp: "9876543230", address: "101 Green Park", dob: "2013-07-14", gender: "male", previous_school: "Delhi Public School", status: "new", created_at: "2024-03-01" },
  { id: 2, student_name: "Diya Nair", class_applied: "8", parent_name: "Suresh Nair", parent_phone: "9876543231", parent_whatsapp: "9876543231", address: "202 Lake View", dob: "2011-11-22", gender: "female", status: "approved", created_at: "2024-02-25" },
  { id: 3, student_name: "Karan Joshi", class_applied: "5", parent_name: "Amit Joshi", parent_phone: "9876543232", parent_whatsapp: "9876543232", address: "303 Hill Road", dob: "2014-01-09", gender: "male", status: "rejected", notes: "Incomplete documents", created_at: "2024-02-20" },
];

export default function AdmissionsPage() {
  const [search, setSearch] = useState("");
  const [statusFilter, setStatusFilter] = useState("all");
  const [selectedAdmission, setSelectedAdmission] = useState<Admission | null>(null);

  const filtered = mockAdmissions.filter((a) => {
    const matchSearch = a.student_name.toLowerCase().includes(search.toLowerCase());
    const matchStatus = statusFilter === "all" || a.status === statusFilter;
    return matchSearch && matchStatus;
  });

  return (
    <div className="space-y-6">
      <PageHeader
        title="Admissions"
        description="Review and manage admission applications"
        action={<Button variant="outline"><Download className="h-4 w-4 mr-2" /> Export</Button>}
      />

      <Card>
        <div className="p-4 border-b flex flex-wrap gap-3">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Search admissions..." value={search} onChange={(e) => setSearch(e.target.value)} className="pl-9" />
          </div>
          <Select value={statusFilter} onValueChange={setStatusFilter}>
            <SelectTrigger className="w-[130px]"><SelectValue placeholder="Status" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Status</SelectItem>
              <SelectItem value="new">New</SelectItem>
              <SelectItem value="approved">Approved</SelectItem>
              <SelectItem value="rejected">Rejected</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Student Name</TableHead>
              <TableHead>Class Applied</TableHead>
              <TableHead>Parent</TableHead>
              <TableHead>Phone</TableHead>
              <TableHead>Status</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filtered.map((a) => (
              <TableRow key={a.id}>
                <TableCell className="font-medium">{a.student_name}</TableCell>
                <TableCell>Class {a.class_applied}</TableCell>
                <TableCell>{a.parent_name}</TableCell>
                <TableCell>{a.parent_phone}</TableCell>
                <TableCell><StatusBadge status={a.status} /></TableCell>
                <TableCell className="text-right space-x-1">
                  <Button variant="ghost" size="icon" onClick={() => setSelectedAdmission(a)}><Eye className="h-4 w-4" /></Button>
                  {a.status === "new" && (
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

      <Dialog open={!!selectedAdmission} onOpenChange={() => setSelectedAdmission(null)}>
        <DialogContent>
          <DialogHeader><DialogTitle>Admission Details</DialogTitle></DialogHeader>
          {selectedAdmission && (
            <div className="space-y-3 text-sm">
              <div className="grid grid-cols-2 gap-3">
                <div><span className="text-muted-foreground">Student:</span> <strong>{selectedAdmission.student_name}</strong></div>
                <div><span className="text-muted-foreground">Class:</span> {selectedAdmission.class_applied}</div>
                <div><span className="text-muted-foreground">Parent:</span> {selectedAdmission.parent_name}</div>
                <div><span className="text-muted-foreground">Phone:</span> {selectedAdmission.parent_phone}</div>
                <div><span className="text-muted-foreground">WhatsApp:</span> {selectedAdmission.parent_whatsapp}</div>
                <div><span className="text-muted-foreground">DOB:</span> {selectedAdmission.dob}</div>
                <div><span className="text-muted-foreground">Gender:</span> {selectedAdmission.gender}</div>
                <div><span className="text-muted-foreground">Previous School:</span> {selectedAdmission.previous_school || "N/A"}</div>
                <div className="col-span-2"><span className="text-muted-foreground">Address:</span> {selectedAdmission.address}</div>
              </div>
              <div className="flex items-center gap-2 pt-2">
                <span className="text-muted-foreground">Status:</span>
                <StatusBadge status={selectedAdmission.status} />
              </div>
              {selectedAdmission.status === "new" && (
                <div className="flex gap-2 pt-3">
                  <Button className="flex-1"><CheckCircle className="h-4 w-4 mr-2" /> Approve</Button>
                  <Button variant="destructive" className="flex-1"><XCircle className="h-4 w-4 mr-2" /> Reject</Button>
                </div>
              )}
            </div>
          )}
        </DialogContent>
      </Dialog>
    </div>
  );
}
