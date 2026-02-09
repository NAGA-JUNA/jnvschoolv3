import { useState, useMemo } from "react";
import { Link } from "react-router-dom";
import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { EmptyState } from "@/components/shared/EmptyState";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Search, Eye, Download, Users, Phone, Calendar } from "lucide-react";
import { SUBJECTS } from "@/types/teacher";
import { mockTeachers } from "@/data/mockTeachers";
import { toast } from "sonner";

export default function InactiveTeachers() {
  const [search, setSearch] = useState("");
  const [subjectFilter, setSubjectFilter] = useState("all");

  const inactive = useMemo(() => {
    return mockTeachers
      .filter((t) => t.status === "inactive")
      .filter((t) => {
        const q = search.toLowerCase();
        const matchSearch = !q || t.full_name.toLowerCase().includes(q) || t.employee_id.toLowerCase().includes(q);
        const matchSubject = subjectFilter === "all" || t.subjects.includes(subjectFilter);
        return matchSearch && matchSubject;
      });
  }, [search, subjectFilter]);

  const getInitials = (name: string) => name.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2);

  return (
    <div className="space-y-6">
      <PageHeader
        title="Inactive / Former Teachers"
        description="View records of inactive or former teaching staff (read-only)"
        action={
          <Button variant="outline" onClick={() => toast.success("Exporting inactive teacher records...")}>
            <Download className="h-4 w-4 mr-2" /> Export Records
          </Button>
        }
      />

      <Card>
        <div className="p-4 border-b flex flex-wrap gap-3">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Search inactive teachers..." value={search} onChange={(e) => setSearch(e.target.value)} className="pl-9" />
          </div>
          <Select value={subjectFilter} onValueChange={setSubjectFilter}>
            <SelectTrigger className="w-[150px]"><SelectValue placeholder="Subject" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Subjects</SelectItem>
              {SUBJECTS.map((s) => <SelectItem key={s} value={s}>{s}</SelectItem>)}
            </SelectContent>
          </Select>
        </div>

        {inactive.length === 0 ? (
          <EmptyState icon={<Users className="h-12 w-12" />} title="No inactive teachers found" description="Teachers marked as inactive will appear here." />
        ) : (
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Teacher</TableHead>
                <TableHead>Employee ID</TableHead>
                <TableHead>Subject(s)</TableHead>
                <TableHead>Qualification</TableHead>
                <TableHead>Joining Date</TableHead>
                <TableHead>Phone</TableHead>
                <TableHead>Status</TableHead>
                <TableHead className="text-right">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {inactive.map((t) => (
                <TableRow key={t.id}>
                  <TableCell>
                    <div className="flex items-center gap-3">
                      <Avatar className="h-9 w-9">
                        <AvatarImage src={t.photo_url} />
                        <AvatarFallback className="text-xs bg-primary/10 text-primary">{getInitials(t.full_name)}</AvatarFallback>
                      </Avatar>
                      <div>
                        <p className="font-medium">{t.full_name}</p>
                        <p className="text-xs text-muted-foreground">{t.email}</p>
                      </div>
                    </div>
                  </TableCell>
                  <TableCell className="font-mono text-sm">{t.employee_id}</TableCell>
                  <TableCell>
                    <div className="flex flex-wrap gap-1">
                      {t.subjects.map((s) => <Badge key={s} variant="outline" className="text-xs">{s}</Badge>)}
                    </div>
                  </TableCell>
                  <TableCell className="text-sm">{t.qualification}</TableCell>
                  <TableCell>
                    <div className="flex items-center gap-1.5 text-sm text-muted-foreground">
                      <Calendar className="h-3.5 w-3.5" /> {new Date(t.joining_date).toLocaleDateString("en-IN")}
                    </div>
                  </TableCell>
                  <TableCell>
                    <div className="flex items-center gap-1 text-sm">
                      <Phone className="h-3.5 w-3.5 text-muted-foreground" /> {t.phone}
                    </div>
                  </TableCell>
                  <TableCell><StatusBadge status={t.status} /></TableCell>
                  <TableCell className="text-right">
                    <Button variant="ghost" size="icon" asChild>
                      <Link to={`/admin/teachers/${t.id}`}><Eye className="h-4 w-4" /></Link>
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
