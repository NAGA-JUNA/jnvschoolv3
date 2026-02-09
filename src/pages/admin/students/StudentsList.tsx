import { useState, useMemo } from "react";
import { Link } from "react-router-dom";
import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { EmptyState } from "@/components/shared/EmptyState";
import { ErrorState } from "@/components/shared/ErrorState";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Checkbox } from "@/components/ui/checkbox";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { Skeleton } from "@/components/ui/skeleton";
import {
  Plus, Search, Download, Eye, Pencil, MessageCircle, UserX, LayoutGrid, LayoutList,
  ChevronLeft, ChevronRight, GraduationCap, MoreVertical, Copy, FileDown, UserCheck, Phone
} from "lucide-react";
import { StudentRecord, CLASSES, SECTIONS, ACADEMIC_YEARS, MESSAGE_TEMPLATES } from "@/types/student";
import { useApi } from "@/hooks/useApi";
import api from "@/api/client";
import { ADMIN } from "@/api/endpoints";
import { toast } from "sonner";

export default function StudentsList() {
  const [search, setSearch] = useState("");
  const [classFilter, setClassFilter] = useState("all");
  const [sectionFilter, setSectionFilter] = useState("all");
  const [yearFilter, setYearFilter] = useState("all");
  const [statusFilter, setStatusFilter] = useState("all");
  const [viewMode, setViewMode] = useState<"table" | "card">("table");
  const [selected, setSelected] = useState<number[]>([]);
  const [page, setPage] = useState(1);
  const [messageDialog, setMessageDialog] = useState<{ open: boolean; student?: StudentRecord }>({ open: false });
  const [deactivateDialog, setDeactivateDialog] = useState<{ open: boolean; student?: StudentRecord }>({ open: false });
  const [selectedTemplate, setSelectedTemplate] = useState("");
  const perPage = 10;

  const { data: students, loading, error, refetch } = useApi<StudentRecord[]>(
    () => api.get<StudentRecord[]>(ADMIN.students),
    []
  );

  const filtered = useMemo(() => {
    if (!students) return [];
    return students.filter((s) => {
      const q = search.toLowerCase();
      const matchSearch = !q || s.full_name.toLowerCase().includes(q) || s.admission_no.toLowerCase().includes(q) || s.primary_phone?.includes(q) || s.whatsapp_number?.includes(q);
      const matchClass = classFilter === "all" || s.class === classFilter;
      const matchSection = sectionFilter === "all" || s.section === sectionFilter;
      const matchYear = yearFilter === "all" || s.academic_year === yearFilter;
      const matchStatus = statusFilter === "all" || s.status === statusFilter;
      return matchSearch && matchClass && matchSection && matchYear && matchStatus;
    });
  }, [students, search, classFilter, sectionFilter, yearFilter, statusFilter]);

  const totalPages = Math.ceil(filtered.length / perPage);
  const paginated = filtered.slice((page - 1) * perPage, page * perPage);

  const toggleSelect = (id: number) => setSelected((p) => p.includes(id) ? p.filter((x) => x !== id) : [...p, id]);
  const toggleAll = () => setSelected(selected.length === paginated.length ? [] : paginated.map((s) => s.id));
  const getInitials = (name: string) => name.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2);

  const handleCopyMessage = (student: StudentRecord) => {
    const tpl = MESSAGE_TEMPLATES.find((t) => t.id === selectedTemplate);
    if (!tpl) { toast.error("Please select a template"); return; }
    const msg = tpl.body
      .replace("{student_name}", student.full_name)
      .replace("{class}", student.class)
      .replace("{section}", student.section)
      .replace("{date}", new Date().toLocaleDateString("en-IN"))
      .replace("{school_name}", "JSchoolAdmin");
    navigator.clipboard.writeText(msg);
    toast.success("Message copied to clipboard! Open WhatsApp to send.");
    setMessageDialog({ open: false });
    setSelectedTemplate("");
  };

  const handleDeactivate = async (student: StudentRecord) => {
    try {
      await api.put(ADMIN.student(student.id), { status: "inactive" });
      toast.success(`${student.full_name} has been deactivated`);
      setDeactivateDialog({ open: false });
      refetch();
    } catch {
      toast.error("Failed to deactivate student");
    }
  };

  const handleBulkExport = () => {
    toast.success(`Exporting ${selected.length} student records...`);
    setSelected([]);
  };

  const handleBulkPromote = () => {
    toast.success(`${selected.length} students promoted successfully`);
    setSelected([]);
  };

  // Loading skeleton
  if (loading) {
    return (
      <div className="space-y-6">
        <PageHeader title="Students Management" description="Manage student records, attendance, and documents" />
        <Card className="p-4 space-y-4">
          {Array.from({ length: 6 }).map((_, i) => (
            <div key={i} className="flex items-center gap-4">
              <Skeleton className="h-9 w-9 rounded-full" />
              <div className="space-y-2 flex-1">
                <Skeleton className="h-4 w-[200px]" />
                <Skeleton className="h-3 w-[150px]" />
              </div>
              <Skeleton className="h-4 w-[80px]" />
              <Skeleton className="h-6 w-[60px]" />
            </div>
          ))}
        </Card>
      </div>
    );
  }

  // Error state
  if (error) {
    return (
      <div className="space-y-6">
        <PageHeader title="Students Management" description="Manage student records, attendance, and documents" />
        <ErrorState message={error} onRetry={refetch} />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <PageHeader
        title="Students Management"
        description={`Manage student records, attendance, and documents • ${students?.length || 0} total students`}
        action={
          <div className="flex gap-2">
            <Button variant="outline" asChild>
              <Link to="/admin/students/import"><Download className="h-4 w-4 mr-2" /> Import</Link>
            </Button>
            <Button asChild>
              <Link to="/admin/students/add"><Plus className="h-4 w-4 mr-2" /> Add Student</Link>
            </Button>
          </div>
        }
      />

      {/* Filters Bar */}
      <Card>
        <div className="p-4 border-b flex flex-wrap gap-3 items-center">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Search by name, admission no, or phone..." value={search} onChange={(e) => { setSearch(e.target.value); setPage(1); }} className="pl-9" />
          </div>
          <Select value={classFilter} onValueChange={(v) => { setClassFilter(v); setPage(1); }}>
            <SelectTrigger className="w-[130px]"><SelectValue placeholder="Class" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Classes</SelectItem>
              {CLASSES.map((c) => <SelectItem key={c} value={c}>Class {c}</SelectItem>)}
            </SelectContent>
          </Select>
          <Select value={sectionFilter} onValueChange={(v) => { setSectionFilter(v); setPage(1); }}>
            <SelectTrigger className="w-[130px]"><SelectValue placeholder="Section" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Sections</SelectItem>
              {SECTIONS.map((s) => <SelectItem key={s} value={s}>Section {s}</SelectItem>)}
            </SelectContent>
          </Select>
          <Select value={yearFilter} onValueChange={(v) => { setYearFilter(v); setPage(1); }}>
            <SelectTrigger className="w-[140px]"><SelectValue placeholder="Year" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Years</SelectItem>
              {ACADEMIC_YEARS.map((y) => <SelectItem key={y} value={y}>{y}</SelectItem>)}
            </SelectContent>
          </Select>
          <Select value={statusFilter} onValueChange={(v) => { setStatusFilter(v); setPage(1); }}>
            <SelectTrigger className="w-[130px]"><SelectValue placeholder="Status" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Status</SelectItem>
              <SelectItem value="active">Active</SelectItem>
              <SelectItem value="inactive">Inactive</SelectItem>
              <SelectItem value="alumni">Alumni</SelectItem>
            </SelectContent>
          </Select>
          <div className="flex border rounded-md">
            <Button variant={viewMode === "table" ? "secondary" : "ghost"} size="icon" className="h-9 w-9 rounded-r-none" onClick={() => setViewMode("table")}><LayoutList className="h-4 w-4" /></Button>
            <Button variant={viewMode === "card" ? "secondary" : "ghost"} size="icon" className="h-9 w-9 rounded-l-none" onClick={() => setViewMode("card")}><LayoutGrid className="h-4 w-4" /></Button>
          </div>
        </div>

        {/* Bulk Actions */}
        {selected.length > 0 && (
          <div className="px-4 py-2 bg-primary/5 border-b flex items-center gap-3 text-sm">
            <span className="font-medium">{selected.length} selected</span>
            <Button size="sm" variant="outline" onClick={handleBulkPromote}><UserCheck className="h-3.5 w-3.5 mr-1.5" /> Promote</Button>
            <Button size="sm" variant="outline" onClick={handleBulkExport}><FileDown className="h-3.5 w-3.5 mr-1.5" /> Export</Button>
            <Button size="sm" variant="ghost" onClick={() => setSelected([])}>Clear</Button>
          </div>
        )}

        {/* Content */}
        {filtered.length === 0 ? (
          <EmptyState icon={<GraduationCap className="h-12 w-12" />} title="No students found" description="Try adjusting your search or filters" />
        ) : viewMode === "table" ? (
          <div className="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead className="w-10"><Checkbox checked={selected.length === paginated.length && paginated.length > 0} onCheckedChange={toggleAll} /></TableHead>
                  <TableHead>Student</TableHead>
                  <TableHead>Student ID</TableHead>
                  <TableHead>Admission No</TableHead>
                  <TableHead>Class & Section</TableHead>
                  <TableHead>Parent Contact</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {paginated.map((s) => (
                  <TableRow key={s.id}>
                    <TableCell><Checkbox checked={selected.includes(s.id)} onCheckedChange={() => toggleSelect(s.id)} /></TableCell>
                    <TableCell>
                      <div className="flex items-center gap-3">
                        <Avatar className="h-9 w-9">
                          <AvatarImage src={s.photo_url} />
                          <AvatarFallback className="text-xs bg-primary/10 text-primary">{getInitials(s.full_name)}</AvatarFallback>
                        </Avatar>
                        <div>
                          <Link to={`/admin/students/${s.id}`} className="font-medium hover:text-primary transition-colors">{s.full_name}</Link>
                          <p className="text-xs text-muted-foreground capitalize">{s.gender} • {new Date(s.dob).toLocaleDateString("en-IN")}</p>
                        </div>
                      </div>
                    </TableCell>
                    <TableCell className="text-sm text-muted-foreground">{s.student_id}</TableCell>
                    <TableCell className="font-mono text-sm">{s.admission_no}</TableCell>
                    <TableCell><span className="font-medium">{s.class}</span>-{s.section}</TableCell>
                    <TableCell>
                      <div className="flex items-center gap-1 text-sm">
                        <Phone className="h-3.5 w-3.5 text-muted-foreground" />
                        {s.whatsapp_number}
                      </div>
                    </TableCell>
                    <TableCell><StatusBadge status={s.status} /></TableCell>
                    <TableCell className="text-right">
                      <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                          <Button variant="ghost" size="icon"><MoreVertical className="h-4 w-4" /></Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                          <DropdownMenuItem asChild><Link to={`/admin/students/${s.id}`}><Eye className="h-4 w-4 mr-2" /> View Profile</Link></DropdownMenuItem>
                          <DropdownMenuItem asChild><Link to={`/admin/students/${s.id}/edit`}><Pencil className="h-4 w-4 mr-2" /> Edit</Link></DropdownMenuItem>
                          <DropdownMenuItem onClick={() => setMessageDialog({ open: true, student: s })}><MessageCircle className="h-4 w-4 mr-2" /> Send Message</DropdownMenuItem>
                          {s.status === "active" && (
                            <DropdownMenuItem className="text-destructive" onClick={() => setDeactivateDialog({ open: true, student: s })}><UserX className="h-4 w-4 mr-2" /> Deactivate</DropdownMenuItem>
                          )}
                        </DropdownMenuContent>
                      </DropdownMenu>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        ) : (
          <div className="p-4 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            {paginated.map((s) => (
              <Card key={s.id} className="p-4 hover:shadow-md transition-shadow">
                <div className="flex items-start gap-3">
                  <Checkbox checked={selected.includes(s.id)} onCheckedChange={() => toggleSelect(s.id)} className="mt-1" />
                  <Avatar className="h-12 w-12">
                    <AvatarImage src={s.photo_url} />
                    <AvatarFallback className="bg-primary/10 text-primary">{getInitials(s.full_name)}</AvatarFallback>
                  </Avatar>
                  <div className="flex-1 min-w-0">
                    <Link to={`/admin/students/${s.id}`} className="font-semibold hover:text-primary transition-colors block truncate">{s.full_name}</Link>
                    <p className="text-xs text-muted-foreground">{s.student_id} • {s.admission_no}</p>
                    <div className="flex items-center gap-2 mt-1">
                      <span className="text-sm font-medium">Class {s.class}-{s.section}</span>
                      <StatusBadge status={s.status} />
                    </div>
                  </div>
                </div>
                <div className="mt-3 pt-3 border-t flex items-center justify-between">
                  <div className="flex items-center gap-1 text-sm text-muted-foreground">
                    <Phone className="h-3.5 w-3.5" /> {s.whatsapp_number}
                  </div>
                  <div className="flex gap-1">
                    <Button variant="ghost" size="icon" asChild><Link to={`/admin/students/${s.id}`}><Eye className="h-4 w-4" /></Link></Button>
                    <Button variant="ghost" size="icon" asChild><Link to={`/admin/students/${s.id}/edit`}><Pencil className="h-4 w-4" /></Link></Button>
                    <Button variant="ghost" size="icon" onClick={() => setMessageDialog({ open: true, student: s })}><MessageCircle className="h-4 w-4" /></Button>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        )}

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="flex items-center justify-between px-4 py-3 border-t">
            <p className="text-sm text-muted-foreground">Showing {(page - 1) * perPage + 1}–{Math.min(page * perPage, filtered.length)} of {filtered.length}</p>
            <div className="flex gap-1">
              <Button variant="outline" size="sm" disabled={page === 1} onClick={() => setPage(page - 1)}><ChevronLeft className="h-4 w-4" /></Button>
              {Array.from({ length: Math.min(totalPages, 5) }, (_, i) => (
                <Button key={i} variant={page === i + 1 ? "default" : "outline"} size="sm" className="w-9" onClick={() => setPage(i + 1)}>{i + 1}</Button>
              ))}
              {totalPages > 5 && <span className="px-2 self-center text-muted-foreground">...</span>}
              <Button variant="outline" size="sm" disabled={page === totalPages} onClick={() => setPage(page + 1)}><ChevronRight className="h-4 w-4" /></Button>
            </div>
          </div>
        )}
      </Card>

      {/* Send Message Dialog */}
      <Dialog open={messageDialog.open} onOpenChange={(open) => { setMessageDialog({ open }); setSelectedTemplate(""); }}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Send WhatsApp Message to {messageDialog.student?.full_name}</DialogTitle>
          </DialogHeader>
          <div className="space-y-4">
            <Select value={selectedTemplate} onValueChange={setSelectedTemplate}>
              <SelectTrigger><SelectValue placeholder="Select message template" /></SelectTrigger>
              <SelectContent>
                {MESSAGE_TEMPLATES.map((t) => <SelectItem key={t.id} value={t.id}>{t.title}</SelectItem>)}
              </SelectContent>
            </Select>
            {selectedTemplate && (
              <div className="p-3 bg-muted rounded-lg text-sm">
                {MESSAGE_TEMPLATES.find((t) => t.id === selectedTemplate)?.body
                  .replace("{student_name}", messageDialog.student?.full_name || "")
                  .replace("{class}", messageDialog.student?.class || "")
                  .replace("{section}", messageDialog.student?.section || "")
                  .replace("{date}", new Date().toLocaleDateString("en-IN"))
                  .replace("{school_name}", "JSchoolAdmin")}
              </div>
            )}
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setMessageDialog({ open: false })}>Cancel</Button>
            <Button onClick={() => messageDialog.student && handleCopyMessage(messageDialog.student)}><Copy className="h-4 w-4 mr-2" /> Copy to Clipboard</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Deactivate Confirmation Dialog */}
      <Dialog open={deactivateDialog.open} onOpenChange={(open) => setDeactivateDialog({ open })}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Deactivate Student</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">
            Are you sure you want to deactivate <strong>{deactivateDialog.student?.full_name}</strong>?
            This action can be reversed later.
          </p>
          <DialogFooter>
            <Button variant="outline" onClick={() => setDeactivateDialog({ open: false })}>Cancel</Button>
            <Button variant="destructive" onClick={() => deactivateDialog.student && handleDeactivate(deactivateDialog.student)}>Deactivate</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
