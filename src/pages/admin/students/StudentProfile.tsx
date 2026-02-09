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
  Download, Upload, Eye, Trash2, FileText, Copy, BookOpen, Clock, CreditCard
} from "lucide-react";
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from "recharts";
import { mockStudents, mockAttendanceSummary, mockExamResults, mockDocuments, mockMessages, mockAttendance } from "@/data/mockStudents";
import { MESSAGE_TEMPLATES } from "@/types/student";
import { toast } from "sonner";

export default function StudentProfile() {
  const { id } = useParams();
  const navigate = useNavigate();
  const student = mockStudents.find((s) => s.id === Number(id));
  const [messageDialog, setMessageDialog] = useState(false);
  const [selectedTemplate, setSelectedTemplate] = useState("");

  if (!student) {
    return <EmptyState title="Student not found" description="The requested student record does not exist" action={<Button asChild><Link to="/admin/students"><ArrowLeft className="h-4 w-4 mr-2" /> Back to Students</Link></Button>} />;
  }

  const getInitials = (name: string) => name.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2);
  const attendanceRate = mockAttendanceSummary.reduce((acc, m) => acc + m.present, 0) / mockAttendanceSummary.reduce((acc, m) => acc + m.total_days, 0) * 100;

  const handleCopy = () => {
    const tpl = MESSAGE_TEMPLATES.find((t) => t.id === selectedTemplate);
    if (!tpl) { toast.error("Select a template"); return; }
    const msg = tpl.body.replace("{student_name}", student.full_name).replace("{class}", student.class).replace("{section}", student.section).replace("{date}", new Date().toLocaleDateString("en-IN")).replace("{school_name}", "JSchoolAdmin");
    navigator.clipboard.writeText(msg);
    toast.success("Message copied to clipboard!");
    setMessageDialog(false);
    setSelectedTemplate("");
  };

  return (
    <div className="space-y-6">
      <PageHeader
        title="Student Profile"
        action={
          <div className="flex gap-2">
            <Button variant="outline" onClick={() => navigate("/admin/students")}><ArrowLeft className="h-4 w-4 mr-2" /> Back</Button>
            <Button variant="outline" asChild><Link to={`/admin/students/${id}/edit`}><Pencil className="h-4 w-4 mr-2" /> Edit</Link></Button>
            <Button onClick={() => setMessageDialog(true)}><MessageCircle className="h-4 w-4 mr-2" /> Message</Button>
          </div>
        }
      />

      {/* Profile Header */}
      <Card>
        <CardContent className="pt-6">
          <div className="flex flex-col sm:flex-row items-start gap-6">
            <Avatar className="h-24 w-24">
              <AvatarImage src={student.photo_url} />
              <AvatarFallback className="text-2xl bg-primary/10 text-primary">{getInitials(student.full_name)}</AvatarFallback>
            </Avatar>
            <div className="flex-1">
              <div className="flex items-center gap-3 flex-wrap">
                <h2 className="text-2xl font-bold">{student.full_name}</h2>
                <StatusBadge status={student.status} />
              </div>
              <p className="text-muted-foreground mt-1">Class {student.class}-{student.section} • Roll No: {student.roll_no} • {student.academic_year}</p>
              <div className="flex flex-wrap gap-4 mt-3">
                <span className="flex items-center gap-1.5 text-sm text-muted-foreground"><User className="h-4 w-4" /> {student.student_id}</span>
                <span className="flex items-center gap-1.5 text-sm text-muted-foreground"><Calendar className="h-4 w-4" /> DOB: {new Date(student.dob).toLocaleDateString("en-IN")}</span>
                <span className="flex items-center gap-1.5 text-sm text-muted-foreground"><Phone className="h-4 w-4" /> {student.whatsapp_number}</span>
              </div>
            </div>
            <div className="flex gap-3 text-center">
              <div className="px-4 py-2 bg-primary/5 rounded-lg">
                <p className="text-2xl font-bold text-primary">{attendanceRate.toFixed(0)}%</p>
                <p className="text-xs text-muted-foreground">Attendance</p>
              </div>
              <div className="px-4 py-2 bg-success/5 rounded-lg">
                <p className="text-2xl font-bold" style={{ color: "hsl(var(--success))" }}>{mockExamResults.length > 0 ? (mockExamResults.reduce((a, r) => a + r.obtained_marks, 0) / mockExamResults.length).toFixed(0) : "—"}</p>
                <p className="text-xs text-muted-foreground">Avg Marks</p>
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
            { value: "attendance", icon: Clock, label: "Attendance" },
            { value: "exams", icon: BookOpen, label: "Exams & Results" },
            { value: "documents", icon: FileText, label: "Documents" },
            { value: "messages", icon: MessageCircle, label: "Messages" },
            { value: "fees", icon: CreditCard, label: "Fees" },
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
                <InfoRow label="Full Name" value={student.full_name} />
                <InfoRow label="Gender" value={student.gender} capitalize />
                <InfoRow label="Date of Birth" value={new Date(student.dob).toLocaleDateString("en-IN", { year: "numeric", month: "long", day: "numeric" })} />
                <InfoRow label="Admission No" value={student.admission_no} />
                <InfoRow label="Student ID" value={student.student_id} />
                <InfoRow label="Class & Section" value={`Class ${student.class}-${student.section}`} />
                <InfoRow label="Roll Number" value={student.roll_no} />
                <InfoRow label="Academic Year" value={student.academic_year} />
              </CardContent>
            </Card>
            <Card>
              <CardHeader><CardTitle className="text-base">Parent / Guardian</CardTitle></CardHeader>
              <CardContent className="space-y-3 text-sm">
                <InfoRow label="Father's Name" value={student.father_name} />
                <InfoRow label="Mother's Name" value={student.mother_name} />
                <div className="flex items-center gap-2"><Phone className="h-4 w-4 text-muted-foreground" /><InfoRow label="Primary Phone" value={student.primary_phone} /></div>
                <div className="flex items-center gap-2"><MessageCircle className="h-4 w-4 text-muted-foreground" /><InfoRow label="WhatsApp" value={student.whatsapp_number} /></div>
                {student.email && <div className="flex items-center gap-2"><Mail className="h-4 w-4 text-muted-foreground" /><InfoRow label="Email" value={student.email} /></div>}
                <div className="flex items-start gap-2"><MapPin className="h-4 w-4 text-muted-foreground mt-0.5" /><InfoRow label="Address" value={student.address} /></div>
                {student.emergency_contact && <InfoRow label="Emergency Contact" value={student.emergency_contact} />}
              </CardContent>
            </Card>
          </div>
        </TabsContent>

        {/* Attendance Tab */}
        <TabsContent value="attendance" className="mt-6 space-y-6">
          <Card>
            <CardHeader><CardTitle className="text-base">Monthly Attendance Summary</CardTitle></CardHeader>
            <CardContent>
              <div className="h-[300px]">
                <ResponsiveContainer width="100%" height="100%">
                  <BarChart data={mockAttendanceSummary}>
                    <CartesianGrid strokeDasharray="3 3" className="stroke-border" />
                    <XAxis dataKey="month" className="text-xs" />
                    <YAxis className="text-xs" />
                    <Tooltip contentStyle={{ borderRadius: "8px", border: "1px solid hsl(var(--border))", background: "hsl(var(--card))" }} />
                    <Legend />
                    <Bar dataKey="present" fill="hsl(var(--success))" name="Present" radius={[4, 4, 0, 0]} />
                    <Bar dataKey="absent" fill="hsl(var(--destructive))" name="Absent" radius={[4, 4, 0, 0]} />
                    <Bar dataKey="late" fill="hsl(var(--warning))" name="Late" radius={[4, 4, 0, 0]} />
                  </BarChart>
                </ResponsiveContainer>
              </div>
            </CardContent>
          </Card>
          <Card>
            <CardHeader><CardTitle className="text-base">Recent Attendance</CardTitle></CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Date</TableHead>
                    <TableHead>Status</TableHead>
                    <TableHead>Marked By</TableHead>
                    <TableHead>Remarks</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {mockAttendance.map((a) => (
                    <TableRow key={a.id}>
                      <TableCell>{new Date(a.date).toLocaleDateString("en-IN")}</TableCell>
                      <TableCell><StatusBadge status={a.status} /></TableCell>
                      <TableCell>{a.marked_by}</TableCell>
                      <TableCell className="text-muted-foreground">{a.remarks || "—"}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>

        {/* Exams Tab */}
        <TabsContent value="exams" className="mt-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle className="text-base">Exam Results</CardTitle>
              <Button variant="outline" size="sm"><Download className="h-4 w-4 mr-2" /> Report Card PDF</Button>
            </CardHeader>
            <CardContent>
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Exam</TableHead>
                    <TableHead>Subject</TableHead>
                    <TableHead>Max Marks</TableHead>
                    <TableHead>Obtained</TableHead>
                    <TableHead>Grade</TableHead>
                    <TableHead>Date</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {mockExamResults.map((r) => (
                    <TableRow key={r.id}>
                      <TableCell className="font-medium">{r.exam_name}</TableCell>
                      <TableCell>{r.subject}</TableCell>
                      <TableCell>{r.max_marks}</TableCell>
                      <TableCell className="font-medium">{r.obtained_marks}</TableCell>
                      <TableCell><Badge variant={r.grade.startsWith("A") ? "default" : "secondary"}>{r.grade}</Badge></TableCell>
                      <TableCell className="text-muted-foreground">{new Date(r.exam_date).toLocaleDateString("en-IN")}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </CardContent>
          </Card>
        </TabsContent>

        {/* Documents Tab */}
        <TabsContent value="documents" className="mt-6">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between">
              <CardTitle className="text-base">Student Documents</CardTitle>
              <Button size="sm"><Upload className="h-4 w-4 mr-2" /> Upload Document</Button>
            </CardHeader>
            <CardContent>
              {mockDocuments.length === 0 ? (
                <EmptyState title="No documents" description="Upload student documents like Aadhaar, birth certificate, etc." />
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
                    {mockDocuments.map((d) => (
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
              {mockMessages.length === 0 ? (
                <EmptyState title="No messages sent" description="Send a WhatsApp message to the parent." />
              ) : (
                <div className="space-y-3">
                  {mockMessages.map((m) => (
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

        {/* Fees Tab */}
        <TabsContent value="fees" className="mt-6">
          <Card>
            <CardContent className="pt-6">
              <EmptyState icon={<CreditCard className="h-12 w-12" />} title="Fee Management Coming Soon" description="Fee tracking and payment integration will be available in a future update." />
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      {/* Message Dialog */}
      <Dialog open={messageDialog} onOpenChange={(o) => { setMessageDialog(o); setSelectedTemplate(""); }}>
        <DialogContent>
          <DialogHeader><DialogTitle>Send WhatsApp Message to {student.full_name}</DialogTitle></DialogHeader>
          <div className="space-y-4">
            <Select value={selectedTemplate} onValueChange={setSelectedTemplate}>
              <SelectTrigger><SelectValue placeholder="Select message template" /></SelectTrigger>
              <SelectContent>{MESSAGE_TEMPLATES.map((t) => <SelectItem key={t.id} value={t.id}>{t.title}</SelectItem>)}</SelectContent>
            </Select>
            {selectedTemplate && (
              <div className="p-3 bg-muted rounded-lg text-sm">
                {MESSAGE_TEMPLATES.find((t) => t.id === selectedTemplate)?.body.replace("{student_name}", student.full_name).replace("{class}", student.class).replace("{section}", student.section).replace("{date}", new Date().toLocaleDateString("en-IN")).replace("{school_name}", "JSchoolAdmin")}
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

function InfoRow({ label, value, capitalize }: { label: string; value: string; capitalize?: boolean }) {
  return (
    <div className="flex justify-between gap-4">
      <span className="text-muted-foreground shrink-0">{label}</span>
      <span className={`font-medium text-right ${capitalize ? "capitalize" : ""}`}>{value}</span>
    </div>
  );
}
