import { useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Badge } from "@/components/ui/badge";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { ArrowLeft, Camera, Loader2, Save, X } from "lucide-react";
import { TeacherRecord, SUBJECTS, QUALIFICATIONS } from "@/types/teacher";
import { CLASSES, SECTIONS } from "@/types/student";
import { mockTeachers } from "@/data/mockTeachers";
import { toast } from "sonner";

export default function TeacherForm() {
  const navigate = useNavigate();
  const { id } = useParams();
  const isEdit = Boolean(id);
  const existing = isEdit ? mockTeachers.find((t) => t.id === Number(id)) : undefined;

  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState<Partial<TeacherRecord>>({
    full_name: existing?.full_name || "",
    employee_id: existing?.employee_id || "",
    gender: existing?.gender || undefined,
    dob: existing?.dob || "",
    phone: existing?.phone || "",
    whatsapp_number: existing?.whatsapp_number || "",
    email: existing?.email || "",
    address: existing?.address || "",
    qualification: existing?.qualification || "",
    experience_years: existing?.experience_years || 0,
    joining_date: existing?.joining_date || "",
    subjects: existing?.subjects || [],
    assigned_classes: existing?.assigned_classes || [],
    employment_type: existing?.employment_type || "full-time",
    status: existing?.status || "active",
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const updateField = (key: keyof TeacherRecord, value: string | number | string[]) => {
    setForm((p) => ({ ...p, [key]: value }));
    if (errors[key]) setErrors((p) => { const n = { ...p }; delete n[key]; return n; });
  };

  const toggleSubject = (subject: string) => {
    const current = form.subjects || [];
    const updated = current.includes(subject) ? current.filter((s) => s !== subject) : [...current, subject];
    updateField("subjects", updated);
  };

  const toggleClass = (cls: string) => {
    const current = form.assigned_classes || [];
    const updated = current.includes(cls) ? current.filter((c) => c !== cls) : [...current, cls];
    updateField("assigned_classes", updated);
  };

  const validate = (): boolean => {
    const e: Record<string, string> = {};
    if (!form.full_name?.trim()) e.full_name = "Full name is required";
    if (!form.employee_id?.trim()) e.employee_id = "Employee ID is required";
    if (!form.gender) e.gender = "Gender is required";
    if (!form.dob) e.dob = "Date of birth is required";
    if (!form.phone?.trim()) e.phone = "Phone number is required";
    else if (!/^\d{10}$/.test(form.phone.trim())) e.phone = "Enter valid 10-digit phone";
    if (!form.whatsapp_number?.trim()) e.whatsapp_number = "WhatsApp number is required";
    else if (!/^\d{10}$/.test(form.whatsapp_number.trim())) e.whatsapp_number = "Enter valid 10-digit phone";
    if (!form.email?.trim()) e.email = "Email is required";
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email.trim())) e.email = "Enter a valid email";
    if (!form.address?.trim()) e.address = "Address is required";
    if (!form.qualification) e.qualification = "Qualification is required";
    if (!form.joining_date) e.joining_date = "Joining date is required";
    if (!form.subjects || form.subjects.length === 0) e.subjects = "Select at least one subject";
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validate()) { toast.error("Please fix the errors below"); return; }
    setSaving(true);
    await new Promise((r) => setTimeout(r, 1200));
    setSaving(false);
    toast.success(isEdit ? "Teacher updated successfully!" : "Teacher added successfully!");
    navigate("/admin/teachers");
  };

  const getInitials = (name: string) => name ? name.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2) : "?";

  // Generate class-section combos
  const classSections = CLASSES.flatMap((c) => SECTIONS.map((s) => `${c}-${s}`));

  return (
    <div className="space-y-6">
      <PageHeader
        title={isEdit ? "Edit Teacher" : "Add New Teacher"}
        description={isEdit ? `Editing ${existing?.full_name || "teacher"} record` : "Fill in the teacher details below"}
        action={
          <Button variant="outline" onClick={() => navigate("/admin/teachers")}>
            <ArrowLeft className="h-4 w-4 mr-2" /> Back to List
          </Button>
        }
      />

      <form onSubmit={handleSubmit}>
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Main Form */}
          <div className="lg:col-span-2 space-y-6">
            {/* Personal Info */}
            <Card>
              <CardHeader><CardTitle className="text-lg">Personal Information</CardTitle></CardHeader>
              <CardContent className="space-y-4">
                {/* Photo */}
                <div className="flex items-center gap-4">
                  <Avatar className="h-20 w-20">
                    <AvatarFallback className="text-xl bg-primary/10 text-primary">{getInitials(form.full_name || "")}</AvatarFallback>
                  </Avatar>
                  <div>
                    <Button type="button" variant="outline" size="sm"><Camera className="h-4 w-4 mr-2" /> Upload Photo</Button>
                    <p className="text-xs text-muted-foreground mt-1">JPG or PNG, max 2MB</p>
                  </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div className="sm:col-span-2">
                    <Label>Full Name <span className="text-destructive">*</span></Label>
                    <Input value={form.full_name} onChange={(e) => updateField("full_name", e.target.value)} placeholder="Enter teacher's full name" className={errors.full_name ? "border-destructive" : ""} />
                    {errors.full_name && <p className="text-xs text-destructive mt-1">{errors.full_name}</p>}
                  </div>
                  <div>
                    <Label>Employee ID <span className="text-destructive">*</span></Label>
                    <Input value={form.employee_id} onChange={(e) => updateField("employee_id", e.target.value)} placeholder="EMP-XXX" className={errors.employee_id ? "border-destructive" : ""} />
                    {errors.employee_id && <p className="text-xs text-destructive mt-1">{errors.employee_id}</p>}
                  </div>
                  <div>
                    <Label>Gender <span className="text-destructive">*</span></Label>
                    <Select value={form.gender} onValueChange={(v) => updateField("gender", v)}>
                      <SelectTrigger className={errors.gender ? "border-destructive" : ""}><SelectValue placeholder="Select gender" /></SelectTrigger>
                      <SelectContent>
                        <SelectItem value="male">Male</SelectItem>
                        <SelectItem value="female">Female</SelectItem>
                        <SelectItem value="other">Other</SelectItem>
                      </SelectContent>
                    </Select>
                    {errors.gender && <p className="text-xs text-destructive mt-1">{errors.gender}</p>}
                  </div>
                  <div>
                    <Label>Date of Birth <span className="text-destructive">*</span></Label>
                    <Input type="date" value={form.dob} onChange={(e) => updateField("dob", e.target.value)} className={errors.dob ? "border-destructive" : ""} />
                    {errors.dob && <p className="text-xs text-destructive mt-1">{errors.dob}</p>}
                  </div>
                  <div>
                    <Label>Phone <span className="text-destructive">*</span></Label>
                    <Input value={form.phone} onChange={(e) => updateField("phone", e.target.value)} placeholder="10-digit phone number" className={errors.phone ? "border-destructive" : ""} />
                    {errors.phone && <p className="text-xs text-destructive mt-1">{errors.phone}</p>}
                  </div>
                  <div>
                    <Label>WhatsApp Number <span className="text-destructive">*</span></Label>
                    <Input value={form.whatsapp_number} onChange={(e) => updateField("whatsapp_number", e.target.value)} placeholder="WhatsApp number" className={errors.whatsapp_number ? "border-destructive" : ""} />
                    {errors.whatsapp_number && <p className="text-xs text-destructive mt-1">{errors.whatsapp_number}</p>}
                  </div>
                  <div>
                    <Label>Email <span className="text-destructive">*</span></Label>
                    <Input type="email" value={form.email} onChange={(e) => updateField("email", e.target.value)} placeholder="Email address" className={errors.email ? "border-destructive" : ""} />
                    {errors.email && <p className="text-xs text-destructive mt-1">{errors.email}</p>}
                  </div>
                  <div className="sm:col-span-2">
                    <Label>Address <span className="text-destructive">*</span></Label>
                    <Textarea value={form.address} onChange={(e) => updateField("address", e.target.value)} placeholder="Full residential address" className={errors.address ? "border-destructive" : ""} rows={3} />
                    {errors.address && <p className="text-xs text-destructive mt-1">{errors.address}</p>}
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Professional Info */}
            <Card>
              <CardHeader><CardTitle className="text-lg">Professional Details</CardTitle></CardHeader>
              <CardContent className="space-y-4">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <Label>Qualification <span className="text-destructive">*</span></Label>
                    <Select value={form.qualification} onValueChange={(v) => updateField("qualification", v)}>
                      <SelectTrigger className={errors.qualification ? "border-destructive" : ""}><SelectValue placeholder="Select qualification" /></SelectTrigger>
                      <SelectContent>
                        {QUALIFICATIONS.map((q) => <SelectItem key={q} value={q}>{q}</SelectItem>)}
                      </SelectContent>
                    </Select>
                    {errors.qualification && <p className="text-xs text-destructive mt-1">{errors.qualification}</p>}
                  </div>
                  <div>
                    <Label>Experience (years)</Label>
                    <Input type="number" min="0" value={form.experience_years} onChange={(e) => updateField("experience_years", parseInt(e.target.value) || 0)} placeholder="Years of experience" />
                  </div>
                  <div>
                    <Label>Joining Date <span className="text-destructive">*</span></Label>
                    <Input type="date" value={form.joining_date} onChange={(e) => updateField("joining_date", e.target.value)} className={errors.joining_date ? "border-destructive" : ""} />
                    {errors.joining_date && <p className="text-xs text-destructive mt-1">{errors.joining_date}</p>}
                  </div>
                  <div>
                    <Label>Employment Type</Label>
                    <Select value={form.employment_type} onValueChange={(v) => updateField("employment_type", v)}>
                      <SelectTrigger><SelectValue /></SelectTrigger>
                      <SelectContent>
                        <SelectItem value="full-time">Full-time</SelectItem>
                        <SelectItem value="part-time">Part-time</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                  <div>
                    <Label>Status</Label>
                    <Select value={form.status} onValueChange={(v) => updateField("status", v)}>
                      <SelectTrigger><SelectValue /></SelectTrigger>
                      <SelectContent>
                        <SelectItem value="active">Active</SelectItem>
                        <SelectItem value="inactive">Inactive</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </div>

                {/* Subjects Multi-select */}
                <div>
                  <Label>Subjects <span className="text-destructive">*</span></Label>
                  <div className={`mt-2 p-3 border rounded-lg ${errors.subjects ? "border-destructive" : "border-border"}`}>
                    <div className="flex flex-wrap gap-2">
                      {SUBJECTS.map((s) => (
                        <Badge
                          key={s}
                          variant={(form.subjects || []).includes(s) ? "default" : "outline"}
                          className="cursor-pointer transition-all hover:shadow-sm"
                          onClick={() => toggleSubject(s)}
                        >
                          {s}
                          {(form.subjects || []).includes(s) && <X className="h-3 w-3 ml-1" />}
                        </Badge>
                      ))}
                    </div>
                  </div>
                  {errors.subjects && <p className="text-xs text-destructive mt-1">{errors.subjects}</p>}
                </div>

                {/* Class Assignment Multi-select */}
                <div>
                  <Label>Assigned Classes</Label>
                  <div className="mt-2 p-3 border rounded-lg border-border max-h-[200px] overflow-y-auto">
                    <div className="flex flex-wrap gap-1.5">
                      {classSections.map((cs) => (
                        <Badge
                          key={cs}
                          variant={(form.assigned_classes || []).includes(cs) ? "default" : "outline"}
                          className="cursor-pointer text-xs transition-all hover:shadow-sm"
                          onClick={() => toggleClass(cs)}
                        >
                          {cs}
                        </Badge>
                      ))}
                    </div>
                  </div>
                  <p className="text-xs text-muted-foreground mt-1">Click to select/deselect class-section assignments</p>
                </div>
              </CardContent>
            </Card>
          </div>

          {/* Sticky Save Panel */}
          <div className="lg:col-span-1">
            <div className="lg:sticky lg:top-6 space-y-4">
              <Card>
                <CardContent className="pt-6 space-y-4">
                  <Button type="submit" className="w-full" disabled={saving}>
                    {saving ? <><Loader2 className="h-4 w-4 mr-2 animate-spin" /> Saving...</> : <><Save className="h-4 w-4 mr-2" /> {isEdit ? "Update Teacher" : "Save Teacher"}</>}
                  </Button>
                  <Button type="button" variant="outline" className="w-full" onClick={() => navigate("/admin/teachers")}>Cancel</Button>
                </CardContent>
              </Card>
              <Card>
                <CardContent className="pt-6">
                  <h4 className="font-medium text-sm mb-2">Quick Tips</h4>
                  <ul className="text-xs text-muted-foreground space-y-1.5">
                    <li>• Employee ID must be unique</li>
                    <li>• WhatsApp number is mandatory for messaging</li>
                    <li>• Select at least one subject</li>
                    <li>• Class assignments can be updated later</li>
                    <li>• Photo upload supports JPG/PNG (max 2MB)</li>
                  </ul>
                </CardContent>
              </Card>
              {(form.subjects || []).length > 0 && (
                <Card>
                  <CardContent className="pt-6">
                    <h4 className="font-medium text-sm mb-2">Selected Subjects ({(form.subjects || []).length})</h4>
                    <div className="flex flex-wrap gap-1.5">
                      {(form.subjects || []).map((s) => <Badge key={s} variant="secondary" className="text-xs">{s}</Badge>)}
                    </div>
                  </CardContent>
                </Card>
              )}
              {(form.assigned_classes || []).length > 0 && (
                <Card>
                  <CardContent className="pt-6">
                    <h4 className="font-medium text-sm mb-2">Assigned Classes ({(form.assigned_classes || []).length})</h4>
                    <div className="flex flex-wrap gap-1.5">
                      {(form.assigned_classes || []).map((c) => <Badge key={c} variant="secondary" className="text-xs">{c}</Badge>)}
                    </div>
                  </CardContent>
                </Card>
              )}
            </div>
          </div>
        </div>
      </form>
    </div>
  );
}
