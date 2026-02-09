import { useState, useMemo } from "react";
import { Link } from "react-router-dom";
import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { EmptyState } from "@/components/shared/EmptyState";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Checkbox } from "@/components/ui/checkbox";
import { Badge } from "@/components/ui/badge";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import {
  Plus, Search, Download, Eye, Pencil, MessageCircle, UserX, LayoutGrid, LayoutList,
  ChevronLeft, ChevronRight, Users, MoreVertical, Copy, FileDown, Phone, BookOpen
} from "lucide-react";
import { TeacherRecord, SUBJECTS, TEACHER_MESSAGE_TEMPLATES } from "@/types/teacher";
import { CLASSES } from "@/types/student";
import { mockTeachers } from "@/data/mockTeachers";
import { toast } from "sonner";

export default function TeachersList() {
  const [search, setSearch] = useState("");
  const [subjectFilter, setSubjectFilter] = useState("all");
  const [classFilter, setClassFilter] = useState("all");
  const [statusFilter, setStatusFilter] = useState("all");
  const [viewMode, setViewMode] = useState<"table" | "card">("table");
  const [selected, setSelected] = useState<number[]>([]);
  const [page, setPage] = useState(1);
  const [messageDialog, setMessageDialog] = useState<{ open: boolean; teacher?: TeacherRecord }>({ open: false });
  const [deactivateDialog, setDeactivateDialog] = useState<{ open: boolean; teacher?: TeacherRecord }>({ open: false });
  const [selectedTemplate, setSelectedTemplate] = useState("");
  const perPage = 10;

  const filtered = useMemo(() => {
    return mockTeachers.filter((t) => {
      const q = search.toLowerCase();
      const matchSearch = !q || t.full_name.toLowerCase().includes(q) || t.employee_id.toLowerCase().includes(q) || t.phone.includes(q);
      const matchSubject = subjectFilter === "all" || t.subjects.includes(subjectFilter);
      const matchClass = classFilter === "all" || t.assigned_classes.some((c) => c.startsWith(classFilter + "-"));
      const matchStatus = statusFilter === "all" || t.status === statusFilter;
      return matchSearch && matchSubject && matchClass && matchStatus;
    });
  }, [search, subjectFilter, classFilter, statusFilter]);

  const totalPages = Math.ceil(filtered.length / perPage);
  const paginated = filtered.slice((page - 1) * perPage, page * perPage);

  const toggleSelect = (id: number) => setSelected((p) => p.includes(id) ? p.filter((x) => x !== id) : [...p, id]);
  const toggleAll = () => setSelected(selected.length === paginated.length ? [] : paginated.map((t) => t.id));
  const getInitials = (name: string) => name.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2);

  const handleCopyMessage = (teacher: TeacherRecord) => {
    const tpl = TEACHER_MESSAGE_TEMPLATES.find((t) => t.id === selectedTemplate);
    if (!tpl) { toast.error("Please select a template"); return; }
    const msg = tpl.body
      .replace("{teacher_name}", teacher.full_name)
      .replace("{date}", new Date().toLocaleDateString("en-IN"))
      .replace("{time}", "10:00 AM")
      .replace("{subject}", teacher.subjects[0] || "")
      .replace("{reason}", "unforeseen circumstances")
      .replace("{school_name}", "JSchoolAdmin");
    navigator.clipboard.writeText(msg);
    toast.success("Message copied to clipboard! Open WhatsApp to send.");
    setMessageDialog({ open: false });
    setSelectedTemplate("");
  };

  const handleDeactivate = (teacher: TeacherRecord) => {
    toast.success(`${teacher.full_name} has been deactivated`);
    setDeactivateDialog({ open: false });
  };

  const handleBulkExport = () => {
    toast.success(`Exporting ${selected.length} teacher records...`);
    setSelected([]);
  };

  return (
    <div className="space-y-6">
      <PageHeader
        title="Teachers Management"
        description="Manage teaching and office staff records"
        action={
          <div className="flex gap-2">
            <Button variant="outline" asChild>
              <Link to="/admin/teachers/import"><Download className="h-4 w-4 mr-2" /> Import</Link>
            </Button>
            <Button asChild>
              <Link to="/admin/teachers/add"><Plus className="h-4 w-4 mr-2" /> Add Teacher</Link>
            </Button>
          </div>
        }
      />

      {/* Filters Bar */}
      <Card>
        <div className="p-4 border-b flex flex-wrap gap-3 items-center">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Search by name, employee ID, or phone..." value={search} onChange={(e) => { setSearch(e.target.value); setPage(1); }} className="pl-9" />
          </div>
          <Select value={subjectFilter} onValueChange={(v) => { setSubjectFilter(v); setPage(1); }}>
            <SelectTrigger className="w-[150px]"><SelectValue placeholder="Subject" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Subjects</SelectItem>
              {SUBJECTS.map((s) => <SelectItem key={s} value={s}>{s}</SelectItem>)}
            </SelectContent>
          </Select>
          <Select value={classFilter} onValueChange={(v) => { setClassFilter(v); setPage(1); }}>
            <SelectTrigger className="w-[130px]"><SelectValue placeholder="Class" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Classes</SelectItem>
              {CLASSES.map((c) => <SelectItem key={c} value={c}>Class {c}</SelectItem>)}
            </SelectContent>
          </Select>
          <Select value={statusFilter} onValueChange={(v) => { setStatusFilter(v); setPage(1); }}>
            <SelectTrigger className="w-[130px]"><SelectValue placeholder="Status" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Status</SelectItem>
              <SelectItem value="active">Active</SelectItem>
              <SelectItem value="inactive">Inactive</SelectItem>
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
            <Button size="sm" variant="outline" onClick={handleBulkExport}><FileDown className="h-3.5 w-3.5 mr-1.5" /> Export</Button>
            <Button size="sm" variant="ghost" onClick={() => setSelected([])}>Clear</Button>
          </div>
        )}

        {/* Content */}
        {filtered.length === 0 ? (
          <EmptyState icon={<Users className="h-12 w-12" />} title="No teachers found" description="Try adjusting your search or filters" />
        ) : viewMode === "table" ? (
          <div className="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead className="w-10"><Checkbox checked={selected.length === paginated.length && paginated.length > 0} onCheckedChange={toggleAll} /></TableHead>
                  <TableHead>Teacher</TableHead>
                  <TableHead>Employee ID</TableHead>
                  <TableHead>Subject(s)</TableHead>
                  <TableHead>Classes</TableHead>
                  <TableHead>Phone</TableHead>
                  <TableHead>Status</TableHead>
                  <TableHead className="text-right">Actions</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {paginated.map((t) => (
                  <TableRow key={t.id}>
                    <TableCell><Checkbox checked={selected.includes(t.id)} onCheckedChange={() => toggleSelect(t.id)} /></TableCell>
                    <TableCell>
                      <div className="flex items-center gap-3">
                        <Avatar className="h-9 w-9">
                          <AvatarImage src={t.photo_url} />
                          <AvatarFallback className="text-xs bg-primary/10 text-primary">{getInitials(t.full_name)}</AvatarFallback>
                        </Avatar>
                        <div>
                          <Link to={`/admin/teachers/${t.id}`} className="font-medium hover:text-primary transition-colors">{t.full_name}</Link>
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
                    <TableCell>
                      <div className="flex flex-wrap gap-1">
                        {t.assigned_classes.slice(0, 3).map((c) => <Badge key={c} variant="secondary" className="text-xs">{c}</Badge>)}
                        {t.assigned_classes.length > 3 && <Badge variant="secondary" className="text-xs">+{t.assigned_classes.length - 3}</Badge>}
                      </div>
                    </TableCell>
                    <TableCell>
                      <div className="flex items-center gap-1 text-sm">
                        <Phone className="h-3.5 w-3.5 text-muted-foreground" />
                        {t.phone}
                      </div>
                    </TableCell>
                    <TableCell><StatusBadge status={t.status} /></TableCell>
                    <TableCell className="text-right">
                      <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                          <Button variant="ghost" size="icon"><MoreVertical className="h-4 w-4" /></Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                          <DropdownMenuItem asChild><Link to={`/admin/teachers/${t.id}`}><Eye className="h-4 w-4 mr-2" /> View Profile</Link></DropdownMenuItem>
                          <DropdownMenuItem asChild><Link to={`/admin/teachers/${t.id}/edit`}><Pencil className="h-4 w-4 mr-2" /> Edit</Link></DropdownMenuItem>
                          <DropdownMenuItem asChild><Link to={`/admin/teachers/${t.id}`}><BookOpen className="h-4 w-4 mr-2" /> Assign Classes</Link></DropdownMenuItem>
                          <DropdownMenuItem onClick={() => setMessageDialog({ open: true, teacher: t })}><MessageCircle className="h-4 w-4 mr-2" /> Send Message</DropdownMenuItem>
                          {t.status === "active" && (
                            <DropdownMenuItem className="text-destructive" onClick={() => setDeactivateDialog({ open: true, teacher: t })}><UserX className="h-4 w-4 mr-2" /> Deactivate</DropdownMenuItem>
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
            {paginated.map((t) => (
              <Card key={t.id} className="p-4 hover:shadow-md transition-shadow">
                <div className="flex items-start gap-3">
                  <Checkbox checked={selected.includes(t.id)} onCheckedChange={() => toggleSelect(t.id)} className="mt-1" />
                  <Avatar className="h-12 w-12">
                    <AvatarImage src={t.photo_url} />
                    <AvatarFallback className="bg-primary/10 text-primary">{getInitials(t.full_name)}</AvatarFallback>
                  </Avatar>
                  <div className="flex-1 min-w-0">
                    <Link to={`/admin/teachers/${t.id}`} className="font-semibold hover:text-primary transition-colors block truncate">{t.full_name}</Link>
                    <p className="text-xs text-muted-foreground">{t.employee_id} • {t.employment_type}</p>
                    <div className="flex items-center gap-2 mt-1 flex-wrap">
                      {t.subjects.slice(0, 2).map((s) => <Badge key={s} variant="outline" className="text-xs">{s}</Badge>)}
                      <StatusBadge status={t.status} />
                    </div>
                  </div>
                </div>
                <div className="mt-3 pt-3 border-t flex items-center justify-between">
                  <div className="flex items-center gap-1 text-sm text-muted-foreground">
                    <Phone className="h-3.5 w-3.5" /> {t.phone}
                  </div>
                  <div className="flex gap-1">
                    <Button variant="ghost" size="icon" asChild><Link to={`/admin/teachers/${t.id}`}><Eye className="h-4 w-4" /></Link></Button>
                    <Button variant="ghost" size="icon" asChild><Link to={`/admin/teachers/${t.id}/edit`}><Pencil className="h-4 w-4" /></Link></Button>
                    <Button variant="ghost" size="icon" onClick={() => setMessageDialog({ open: true, teacher: t })}><MessageCircle className="h-4 w-4" /></Button>
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
              {Array.from({ length: totalPages }, (_, i) => (
                <Button key={i} variant={page === i + 1 ? "default" : "outline"} size="sm" className="w-9" onClick={() => setPage(i + 1)}>{i + 1}</Button>
              ))}
              <Button variant="outline" size="sm" disabled={page === totalPages} onClick={() => setPage(page + 1)}><ChevronRight className="h-4 w-4" /></Button>
            </div>
          </div>
        )}
      </Card>

      {/* Send Message Dialog */}
      <Dialog open={messageDialog.open} onOpenChange={(open) => { setMessageDialog({ open }); setSelectedTemplate(""); }}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Send WhatsApp Message to {messageDialog.teacher?.full_name}</DialogTitle>
          </DialogHeader>
          <div className="space-y-4">
            <Select value={selectedTemplate} onValueChange={setSelectedTemplate}>
              <SelectTrigger><SelectValue placeholder="Select message template" /></SelectTrigger>
              <SelectContent>
                {TEACHER_MESSAGE_TEMPLATES.map((t) => <SelectItem key={t.id} value={t.id}>{t.title}</SelectItem>)}
              </SelectContent>
            </Select>
            {selectedTemplate && (
              <div className="p-3 bg-muted rounded-lg text-sm">
                {TEACHER_MESSAGE_TEMPLATES.find((t) => t.id === selectedTemplate)?.body
                  .replace("{teacher_name}", messageDialog.teacher?.full_name || "")
                  .replace("{date}", new Date().toLocaleDateString("en-IN"))
                  .replace("{time}", "10:00 AM")
                  .replace("{subject}", messageDialog.teacher?.subjects[0] || "")
                  .replace("{reason}", "unforeseen circumstances")
                  .replace("{school_name}", "JSchoolAdmin")}
              </div>
            )}
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setMessageDialog({ open: false })}>Cancel</Button>
            <Button onClick={() => messageDialog.teacher && handleCopyMessage(messageDialog.teacher)}><Copy className="h-4 w-4 mr-2" /> Copy to Clipboard</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>

      {/* Deactivate Confirmation Dialog */}
      <Dialog open={deactivateDialog.open} onOpenChange={(open) => setDeactivateDialog({ open })}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Deactivate Teacher</DialogTitle>
          </DialogHeader>
          <p className="text-sm text-muted-foreground">
            Are you sure you want to deactivate <strong>{deactivateDialog.teacher?.full_name}</strong>?
            This will mark them as inactive. This action can be reversed later.
          </p>
          <DialogFooter>
            <Button variant="outline" onClick={() => setDeactivateDialog({ open: false })}>Cancel</Button>
            <Button variant="destructive" onClick={() => deactivateDialog.teacher && handleDeactivate(deactivateDialog.teacher)}>Deactivate</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
