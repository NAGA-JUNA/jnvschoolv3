import { useState, useMemo } from "react";
import { Link } from "react-router-dom";
import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { EmptyState } from "@/components/shared/EmptyState";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Search, Eye, Download, GraduationCap, Phone, Calendar } from "lucide-react";
import { CLASSES, ACADEMIC_YEARS } from "@/types/student";
import { mockStudents } from "@/data/mockStudents";
import { toast } from "sonner";

export default function AlumniPage() {
  const [search, setSearch] = useState("");
  const [classFilter, setClassFilter] = useState("all");
  const [yearFilter, setYearFilter] = useState("all");

  const alumni = useMemo(() => {
    return mockStudents
      .filter((s) => s.status === "alumni")
      .filter((s) => {
        const q = search.toLowerCase();
        const matchSearch = !q || s.full_name.toLowerCase().includes(q) || s.admission_no.toLowerCase().includes(q);
        const matchClass = classFilter === "all" || s.class === classFilter;
        const matchYear = yearFilter === "all" || s.academic_year === yearFilter;
        return matchSearch && matchClass && matchYear;
      });
  }, [search, classFilter, yearFilter]);

  const getInitials = (name: string) => name.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2);

  return (
    <div className="space-y-6">
      <PageHeader
        title="Alumni Records"
        description="View past student records (read-only)"
        action={
          <Button variant="outline" onClick={() => toast.success("Exporting alumni records...")}>
            <Download className="h-4 w-4 mr-2" /> Export Alumni
          </Button>
        }
      />

      <Card>
        <div className="p-4 border-b flex flex-wrap gap-3">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Search alumni..." value={search} onChange={(e) => setSearch(e.target.value)} className="pl-9" />
          </div>
          <Select value={classFilter} onValueChange={setClassFilter}>
            <SelectTrigger className="w-[130px]"><SelectValue placeholder="Class" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Classes</SelectItem>
              {CLASSES.map((c) => <SelectItem key={c} value={c}>Class {c}</SelectItem>)}
            </SelectContent>
          </Select>
          <Select value={yearFilter} onValueChange={setYearFilter}>
            <SelectTrigger className="w-[140px]"><SelectValue placeholder="Year" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Years</SelectItem>
              {ACADEMIC_YEARS.map((y) => <SelectItem key={y} value={y}>{y}</SelectItem>)}
            </SelectContent>
          </Select>
        </div>

        {alumni.length === 0 ? (
          <EmptyState icon={<GraduationCap className="h-12 w-12" />} title="No alumni records found" description="Students moved to alumni status will appear here." />
        ) : (
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Student</TableHead>
                <TableHead>Admission No</TableHead>
                <TableHead>Last Class</TableHead>
                <TableHead>Academic Year</TableHead>
                <TableHead>Parent Contact</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {alumni.map((s) => (
                <TableRow key={s.id}>
                  <TableCell>
                    <div className="flex items-center gap-3">
                      <Avatar className="h-9 w-9">
                        <AvatarImage src={s.photo_url} />
                        <AvatarFallback className="text-xs bg-primary/10 text-primary">{getInitials(s.full_name)}</AvatarFallback>
                      </Avatar>
                      <div>
                        <p className="font-medium">{s.full_name}</p>
                        <p className="text-xs text-muted-foreground capitalize">{s.gender}</p>
                      </div>
                    </div>
                  </TableCell>
                  <TableCell className="font-mono text-sm">{s.admission_no}</TableCell>
                  <TableCell>Class {s.class}-{s.section}</TableCell>
                  <TableCell>
                    <div className="flex items-center gap-1.5 text-sm text-muted-foreground">
                      <Calendar className="h-3.5 w-3.5" /> {s.academic_year}
                    </div>
                  </TableCell>
                  <TableCell>
                    <div className="flex items-center gap-1 text-sm">
                      <Phone className="h-3.5 w-3.5 text-muted-foreground" /> {s.whatsapp_number}
                    </div>
                  </TableCell>
                  <TableCell><StatusBadge status={s.status} /></TableCell>
                  <TableCell className="text-right">
                    <Button variant="ghost" size="icon" asChild>
                      <Link to={`/admin/students/${s.id}`}><Eye className="h-4 w-4" /></Link>
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        )}
      </Card>
    </div>
  );
}
