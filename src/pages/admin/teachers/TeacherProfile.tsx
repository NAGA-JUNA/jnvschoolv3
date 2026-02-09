import { useState } from "react";
import { useParams, Link, useNavigate } from "react-router-dom";
import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from "@/components/ui/dialog";
import { EmptyState } from "@/components/shared/EmptyState";
import {
  ArrowLeft, Pencil, MessageCircle, Phone, Mail, MapPin, Calendar, User,
  Download, Upload, Eye, Trash2, FileText, Copy, BookOpen, Clock, Briefcase, GraduationCap
} from "lucide-react";
import { mockTeachers, mockTeacherDocuments, mockTeacherMessages } from "@/data/mockTeachers";
import { TEACHER_MESSAGE_TEMPLATES } from "@/types/teacher";
import { toast } from "sonner";

function InfoRow({ label, value, capitalize }: { label: string; value: string; capitalize?: boolean }) {
  return (
    <div className="flex justify-between py-1.5 border-b border-border/50 last:border-0">
      <span className="text-muted-foreground">{label}</span>
      <span className={`font-medium text-right ${capitalize ? "capitalize" : ""}`}>{value || "—"}</span>
    </div>
  );
}

export default function TeacherProfile() {
  const { id } = useParams();
  const navigate = useNavigate();
  const teacher = mockTeachers.find((t) => t.id === Number(id));
  const [messageDialog, setMessageDialog] = useState(false);
  const [selectedTemplate, setSelectedTemplate] = useState("");

  if (!teacher) {
    return <EmptyState title="Teacher not found" description="The requested teacher record does not exist" action={<Button asChild><Link to="/admin/teachers"><ArrowLeft className="h-4 w-4 mr-2" /> Back to Teachers</Link></Button>} />;
  }

  const getInitials = (name: string) => name.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2);
  const documents = mockTeacherDocuments.filter((d) => d.teacher_id === teacher.id);
  const messages = mockTeacherMessages.filter((m) => m.teacher_id === teacher.id);

  const handleCopy = () => {
    const tpl = TEACHER_MESSAGE_TEMPLATES.find((t) => t.id === selectedTemplate);
    if (!tpl) { toast.error("Select a template"); return; }
    const msg = tpl.body
      .replace("{teacher_name}", teacher.full_name)
      .replace("{date}", new Date().toLocaleDateString("en-IN"))
      .replace("{time}", "10:00 AM")
      .replace("{subject}", teacher.subjects[0] || "")
      .replace("{reason}", "unforeseen circumstances")
      .replace("{school_name}", "JSchoolAdmin");
    navigator.clipboard.writeText(msg);
    toast.success("Message copied to clipboard!");
    setMessageDialog(false);
    setSelectedTemplate("");
  };

  return (
    <div className="space-y-6">
      <PageHeader
        title="Teacher Profile"
        action={
          <div className="flex gap-2">
            <Button variant="outline" onClick={() => navigate("/admin/teachers")}><ArrowLeft className="h-4 w-4 mr-2" /> Back</Button>
            <Button variant="outline" asChild><Link to={`/admin/teachers/${id}/edit`}><Pencil className="h-4 w-4 mr-2" /> Edit</Link></Button>
            <Button onClick={() => setMessageDialog(true)}><MessageCircle className="h-4 w-4 mr-2" /> Message</Button>
          </div>
        }
      />

      {/* Profile Header */}
      <Card>
        <CardContent className="pt-6">
          <div className="flex flex-col sm:flex-row items-start gap-6">
            <Avatar className="h-24 w-24">
              <AvatarImage src={teacher.photo_url} />
              <AvatarFallback className="text-2xl bg-primary/10 text-primary">{getInitials(teacher.full_name)}</AvatarFallback>
            </Avatar>
            <div className="flex-1">
              <div className="flex items-center gap-3 flex-wrap">
                <h2 className="text-2xl font-bold">{teacher.full_name}</h2>
                <StatusBadge status={teacher.status} />
                <Badge variant="outline" className="capitalize">{teacher.employment_type}</Badge>
              </div>
              <div className="flex flex-wrap gap-1.5 mt-2">
                {teacher.subjects.map((s) => <Badge key={s} variant="secondary">{s}</Badge>)}
              </div>
              <div className="flex flex-wrap gap-4 mt-3">
                <span className="flex items-center gap-1.5 text-sm text-muted-foreground"><Briefcase className="h-4 w-4" /> {teacher.employee_id}</span>
                <span className="flex items-center gap-1.5 text-sm text-muted-foreground"><GraduationCap className="h-4 w-4" /> {teacher.qualification}</span>
                <span className="flex items-center gap-1.5 text-sm text-muted-foreground"><Phone className="h-4 w-4" /> {teacher.phone}</span>
                <span className="flex items-center gap-1.5 text-sm text-muted-foreground"><Mail className="h-4 w-4" /> {teacher.email}</span>
              </div>
            </div>
            <div className="flex gap-3 text-center">
              <div className="px-4 py-2 bg-primary/5 rounded-lg">
                <p className="text-2xl font-bold text-primary">{teacher.experience_years}</p>
                <p className="text-xs text-muted-foreground">Years Exp.</p>
              </div>
              <div className="px-4 py-2 bg-primary/5 rounded-lg">
                <p className="text-2xl font-bold text-primary">{teacher.assigned_classes.length}</p>
                <p className="text-xs text-muted-foreground">Classes</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Tabs */}
      <Tabs defaultValue="overview">
        <TabsList className="w-full justify-start flex-wrap h-auto gap-1 bg-transparent p-0">
          {[
            { value: "overview", icon: User, label: "Overview" },
            { value: "classes", icon: BookOpen, label: "Assigned Classes" },
            { value: "attendance", icon: Clock, label: "Attendance" },
            { value: "documents", icon: FileText, label: "Documents" },
            { value: "messages", icon: MessageCircle, label: "Messages" },
          ].map((t) => (
            <TabsTrigger key={t.value} value={t.value} className="data-[state=active]:bg-primary data-[state=active]:text-primary-foreground rounded-lg px-4">
              <t.icon className="h-4 w-4 mr-1.5" /> {t.label}
            </TabsTrigger>
          ))}
        </TabsList>

        {/* Overview Tab */}
        <TabsContent value="overview" className="mt-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Card>
              <CardHeader><CardTitle className="text-base">Personal Information</CardTitle></CardHeader>
              <CardContent className="space-y-3 text-sm">
                <InfoRow label="Full Name" value={teacher.full_name} />
                <InfoRow label="Gender" value={teacher.gender} capitalize />
                <InfoRow label="Date of Birth" value={new Date(teacher.dob).toLocaleDateString("en-IN", { year: "numeric", month: "long", day: "numeric" })} />
                <InfoRow label="Employee ID" value={teacher.employee_id} />
                <div className="flex items-center gap-2"><Phone className="h-4 w-4 text-muted-foreground" /><InfoRow label="Phone" value={teacher.phone} /></div>
                <div className="flex items-center gap-2"><MessageCircle className="h-4 w-4 text-muted-foreground" /><InfoRow label="WhatsApp" value={teacher.whatsapp_number} /></div>
                <div className="flex items-center gap-2"><Mail className="h-4 w-4 text-muted-foreground" /><InfoRow label="Email" value={teacher.email} /></div>
                <div className="flex items-start gap-2"><MapPin className="h-4 w-4 text-muted-foreground mt-0.5" /><InfoRow label="Address" value={teacher.address} /></div>
              </CardContent>
            </Card>
            <Card>
              <CardHeader><CardTitle className="text-base">Professional Details</CardTitle></CardHeader>
              <CardContent className="space-y-3 text-sm">
                <InfoRow label="Qualification" value={teacher.qualification} />
                <InfoRow label="Experience" value={`${teacher.experience_years} years`} />
                <InfoRow label="Joining Date" value={new Date(teacher.joining_date).toLocaleDateString("en-IN", { year: "numeric", month: "long", day: "numeric" })} />
                <InfoRow label="Employment Type" value={teacher.employment_type} capitalize />
                <InfoRow label="Status" value={teacher.status} capitalize />
                <div className="py-1.5">
                  <span className="text-muted-foreground block mb-1.5">Subjects</span>
                  <div className="flex flex-wrap gap-1.5">
                    {teacher.subjects.map((s) => <Badge key={s} variant="secondary" className="text-xs">{s}</Badge>)}
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        {/* Assigned Classes Tab */}
        <TabsContent value="classes" className="mt-6">
          <Card>
            <CardHeader>
              <CardTitle className="text-base">Class & Section Assignments</CardTitle>
            </CardHeader>
            <CardContent>
              {teacher.assigned_classes.length === 0 ? (
                <EmptyState title="No classes assigned" description="Assign classes to this teacher from the edit page." />
              ) : (
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Class</TableHead>
                      <TableHead>Section</TableHead>
                      <TableHead>Subject(s)</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {teacher.assigned_classes.map((cs) => {
                      const [cls, sec] = cs.split("-");
                      return (
                        <TableRow key={cs}>
                          <TableCell className="font-medium">Class {cls}</TableCell>
                          <TableCell>Section {sec}</TableCell>
                          <TableCell>
                            <div className="flex flex-wrap gap-1">
                              {teacher.subjects.map((s) => <Badge key={s} variant="outline" className="text-xs">{s}</Badge>)}
                            </div>
                          </TableCell>
                        </TableRow>
                      );
                    })}
                  </TableBody>
                </Table>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {/* Attendance Tab */}
        <TabsContent value="attendance" className="mt-6">
          <Card>
            <CardContent className="pt-6">
              <EmptyState icon={<Clock className="h-12 w-12" />} title="Staff Attendance Coming Soon" description="Staff attendance tracking will be available in a future update." />
            </CardContent>
          </Card>
        </TabsContent>

        {/* Documents Tab */}
        <TabsContent value="documents" className="mt-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle className="text-base">Teacher Documents</CardTitle>
              <Button size="sm"><Upload className="h-4 w-4 mr-2" /> Upload Document</Button>
            </CardHeader>
            <CardContent>
              {documents.length === 0 ? (
                <EmptyState title="No documents" description="Upload teacher documents like certificates, resume, etc." />
              ) : (
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Document</TableHead>
                      <TableHead>Type</TableHead>
                      <TableHead>Size</TableHead>
                      <TableHead>Uploaded By</TableHead>
                      <TableHead>Date</TableHead>
                      <TableHead className="text-right">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {documents.map((d) => (
                      <TableRow key={d.id}>
                        <TableCell className="font-medium">{d.name}</TableCell>
                        <TableCell><Badge variant="outline" className="capitalize">{d.type.replace("_", " ")}</Badge></TableCell>
                        <TableCell className="text-muted-foreground">{d.file_size}</TableCell>
                        <TableCell>{d.uploaded_by}</TableCell>
                        <TableCell className="text-muted-foreground">{new Date(d.uploaded_at).toLocaleDateString("en-IN")}</TableCell>
                        <TableCell className="text-right">
                          <Button variant="ghost" size="icon"><Eye className="h-4 w-4" /></Button>
                          <Button variant="ghost" size="icon"><Download className="h-4 w-4" /></Button>
                          <Button variant="ghost" size="icon"><Trash2 className="h-4 w-4 text-destructive" /></Button>
                        </TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        {/* Messages Tab */}
        <TabsContent value="messages" className="mt-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle className="text-base">Message History</CardTitle>
              <Button size="sm" onClick={() => setMessageDialog(true)}><MessageCircle className="h-4 w-4 mr-2" /> New Message</Button>
            </CardHeader>
            <CardContent>
              {messages.length === 0 ? (
                <EmptyState title="No messages sent" description="Send a WhatsApp message to this teacher." />
              ) : (
                <div className="space-y-3">
                  {messages.map((m) => (
                    <div key={m.id} className="p-3 border rounded-lg">
                      <div className="flex items-center justify-between mb-1">
                        <Badge variant="outline">{m.template}</Badge>
                        <span className="text-xs text-muted-foreground">{m.sent_at}</span>
                      </div>
                      <p className="text-sm">{m.message}</p>
                      <p className="text-xs text-muted-foreground mt-1">Sent by {m.sent_by} via {m.channel}</p>
                    </div>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      {/* Message Dialog */}
      <Dialog open={messageDialog} onOpenChange={(o) => { setMessageDialog(o); setSelectedTemplate(""); }}>
        <DialogContent>
          <DialogHeader><DialogTitle>Send WhatsApp Message to {teacher.full_name}</DialogTitle></DialogHeader>
          <div className="space-y-4">
            <Select value={selectedTemplate} onValueChange={setSelectedTemplate}>
              <SelectTrigger><SelectValue placeholder="Select message template" /></SelectTrigger>
              <SelectContent>{TEACHER_MESSAGE_TEMPLATES.map((t) => <SelectItem key={t.id} value={t.id}>{t.title}</SelectItem>)}</SelectContent>
            </Select>
            {selectedTemplate && (
              <div className="p-3 bg-muted rounded-lg text-sm">
                {TEACHER_MESSAGE_TEMPLATES.find((t) => t.id === selectedTemplate)?.body
                  .replace("{teacher_name}", teacher.full_name)
                  .replace("{date}", new Date().toLocaleDateString("en-IN"))
                  .replace("{time}", "10:00 AM")
                  .replace("{subject}", teacher.subjects[0] || "")
                  .replace("{reason}", "unforeseen circumstances")
                  .replace("{school_name}", "JSchoolAdmin")}
              </div>
            )}
          </div>
          <DialogFooter>
            <Button variant="outline" onClick={() => setMessageDialog(false)}>Cancel</Button>
            <Button onClick={handleCopy}><Copy className="h-4 w-4 mr-2" /> Copy to Clipboard</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
