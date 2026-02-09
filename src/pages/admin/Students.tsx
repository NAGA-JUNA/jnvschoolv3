import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog";
import { Plus, Search, Download, Pencil, Trash2 } from "lucide-react";
import { Student } from "@/types";

const mockStudents: Student[] = [
  { id: 1, name: "Rahul Sharma", class: "10", section: "A", roll_no: "1001", gender: "male", dob: "2009-05-15", parent_name: "Vikram Sharma", parent_phone: "9876543220", parent_whatsapp: "9876543220", address: "123 Main Street", status: "active", created_at: "2024-01-10" },
  { id: 2, name: "Sneha Patel", class: "10", section: "B", roll_no: "1002", gender: "female", dob: "2009-08-22", parent_name: "Ramesh Patel", parent_phone: "9876543221", parent_whatsapp: "9876543221", address: "456 Park Road", status: "active", created_at: "2024-01-12" },
  { id: 3, name: "Arjun Singh", class: "9", section: "A", roll_no: "902", gender: "male", dob: "2010-03-08", parent_name: "Baldev Singh", parent_phone: "9876543222", parent_whatsapp: "9876543222", address: "789 Oak Avenue", status: "transferred", created_at: "2023-06-15" },
];

export default function StudentsPage() {
  const [search, setSearch] = useState("");
  const [classFilter, setClassFilter] = useState("all");
  const [statusFilter, setStatusFilter] = useState("all");
  const [dialogOpen, setDialogOpen] = useState(false);

  const students = mockStudents.filter((s) => {
    const matchesSearch = s.name.toLowerCase().includes(search.toLowerCase());
    const matchesClass = classFilter === "all" || s.class === classFilter;
    const matchesStatus = statusFilter === "all" || s.status === statusFilter;
    return matchesSearch && matchesClass && matchesStatus;
  });

  return (
    <div className="space-y-6">
      <PageHeader
        title="Students Management"
        description="Manage student records"
        action={
          <div className="flex gap-2">
            <Button variant="outline"><Download className="h-4 w-4 mr-2" /> Export</Button>
            <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
              <DialogTrigger asChild>
                <Button><Plus className="h-4 w-4 mr-2" /> Add Student</Button>
              </DialogTrigger>
              <DialogContent className="max-w-2xl">
                <DialogHeader><DialogTitle>Add New Student</DialogTitle></DialogHeader>
                <form className="space-y-4">
                  <div className="grid grid-cols-2 gap-4">
                    <div><Label>Full Name</Label><Input placeholder="Student name" /></div>
                    <div><Label>Class</Label><Input placeholder="Class" /></div>
                    <div><Label>Section</Label><Input placeholder="Section" /></div>
                    <div><Label>Roll No</Label><Input placeholder="Roll number" /></div>
                    <div><Label>Gender</Label>
                      <Select><SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                        <SelectContent><SelectItem value="male">Male</SelectItem><SelectItem value="female">Female</SelectItem><SelectItem value="other">Other</SelectItem></SelectContent>
                      </Select>
                    </div>
                    <div><Label>Date of Birth</Label><Input type="date" /></div>
                    <div><Label>Parent Name</Label><Input placeholder="Parent/Guardian" /></div>
                    <div><Label>Parent Phone</Label><Input placeholder="Phone" /></div>
                    <div><Label>Parent WhatsApp</Label><Input placeholder="WhatsApp (mandatory)" /></div>
                    <div><Label>Address</Label><Input placeholder="Address" /></div>
                  </div>
                  <div className="flex justify-end gap-2">
                    <Button variant="outline" type="button" onClick={() => setDialogOpen(false)}>Cancel</Button>
                    <Button type="submit">Save Student</Button>
                  </div>
                </form>
              </DialogContent>
            </Dialog>
          </div>
        }
      />

      <Card>
        <div className="p-4 border-b flex flex-wrap gap-3">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Search students..." value={search} onChange={(e) => setSearch(e.target.value)} className="pl-9" />
          </div>
          <Select value={classFilter} onValueChange={setClassFilter}>
            <SelectTrigger className="w-[120px]"><SelectValue placeholder="Class" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Classes</SelectItem>
              {["1","2","3","4","5","6","7","8","9","10","11","12"].map((c) => <SelectItem key={c} value={c}>Class {c}</SelectItem>)}
            </SelectContent>
          </Select>
          <Select value={statusFilter} onValueChange={setStatusFilter}>
            <SelectTrigger className="w-[130px]"><SelectValue placeholder="Status" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Status</SelectItem>
              <SelectItem value="active">Active</SelectItem>
              <SelectItem value="inactive">Inactive</SelectItem>
              <SelectItem value="transferred">Transferred</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Name</TableHead>
              <TableHead>Class</TableHead>
              <TableHead>Section</TableHead>
              <TableHead>Roll No</TableHead>
              <TableHead>Parent</TableHead>
              <TableHead>Status</TableHead>
              <TableHead className="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {students.map((s) => (
              <TableRow key={s.id}>
                <TableCell className="font-medium">{s.name}</TableCell>
                <TableCell>{s.class}</TableCell>
                <TableCell>{s.section}</TableCell>
                <TableCell>{s.roll_no}</TableCell>
                <TableCell>{s.parent_name}</TableCell>
                <TableCell><StatusBadge status={s.status} /></TableCell>
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
