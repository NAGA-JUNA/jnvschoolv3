import { useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { ArrowLeft, Camera, Loader2, Save } from "lucide-react";
import { CLASSES, SECTIONS, ACADEMIC_YEARS, StudentRecord } from "@/types/student";
import { mockStudents } from "@/data/mockStudents";
import { toast } from "sonner";

export default function StudentForm() {
  const navigate = useNavigate();
  const { id } = useParams();
  const isEdit = Boolean(id);
  const existing = isEdit ? mockStudents.find((s) => s.id === Number(id)) : undefined;

  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState<Partial<StudentRecord>>({
    full_name: existing?.full_name || "",
    gender: existing?.gender || undefined,
    dob: existing?.dob || "",
    admission_no: existing?.admission_no || "",
    roll_no: existing?.roll_no || "",
    class: existing?.class || "",
    section: existing?.section || "",
    academic_year: existing?.academic_year || "2025-26",
    status: existing?.status || "active",
    father_name: existing?.father_name || "",
    mother_name: existing?.mother_name || "",
    primary_phone: existing?.primary_phone || "",
    whatsapp_number: existing?.whatsapp_number || "",
    email: existing?.email || "",
    address: existing?.address || "",
    emergency_contact: existing?.emergency_contact || "",
  });

  const [errors, setErrors] = useState<Record<string, string>>({});

  const updateField = (key: keyof StudentRecord, value: string) => {
    setForm((p) => ({ ...p, [key]: value }));
    if (errors[key]) setErrors((p) => { const n = { ...p }; delete n[key]; return n; });
  };

  const validate = (): boolean => {
    const e: Record<string, string> = {};
    if (!form.full_name?.trim()) e.full_name = "Full name is required";
    if (!form.gender) e.gender = "Gender is required";
    if (!form.dob) e.dob = "Date of birth is required";
    if (!form.admission_no?.trim()) e.admission_no = "Admission number is required";
    if (!form.class) e.class = "Class is required";
    if (!form.section) e.section = "Section is required";
    if (!form.father_name?.trim()) e.father_name = "Father name is required";
    if (!form.primary_phone?.trim()) e.primary_phone = "Primary phone is required";
    else if (!/^\d{10}$/.test(form.primary_phone.trim())) e.primary_phone = "Enter valid 10-digit phone";
    if (!form.whatsapp_number?.trim()) e.whatsapp_number = "WhatsApp number is required";
    else if (!/^\d{10}$/.test(form.whatsapp_number.trim())) e.whatsapp_number = "Enter valid 10-digit phone";
    if (!form.address?.trim()) e.address = "Address is required";
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validate()) { toast.error("Please fix the errors below"); return; }
    setSaving(true);
    // Simulate API call
    await new Promise((r) => setTimeout(r, 1200));
    setSaving(false);
    toast.success(isEdit ? "Student updated successfully!" : "Student added successfully!");
    navigate("/admin/students");
  };

  const getInitials = (name: string) => name ? name.split(" ").map((w) => w[0]).join("").toUpperCase().slice(0, 2) : "?";

  return (
    <div className="space-y-6">
      <PageHeader
        title={isEdit ? "Edit Student" : "Add New Student"}
        description={isEdit ? `Editing ${existing?.full_name || "student"} record` : "Fill in the student details below"}
        action={
          <Button variant="outline" onClick={() => navigate("/admin/students")}>
            <ArrowLeft className="h-4 w-4 mr-2" /> Back to List
          </Button>
        }
      />

      <form onSubmit={handleSubmit}>
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Photo + Basic */}
          <div className="lg:col-span-2 space-y-6">
            <Card>
              <CardHeader><CardTitle className="text-lg">Student Information</CardTitle></CardHeader>
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
                    <Input value={form.full_name} onChange={(e) => updateField("full_name", e.target.value)} placeholder="Enter student's full name" className={errors.full_name ? "border-destructive" : ""} />
                    {errors.full_name && <p className="text-xs text-destructive mt-1">{errors.full_name}</p>}
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
                    <Label>Admission Number <span className="text-destructive">*</span></Label>
                    <Input value={form.admission_no} onChange={(e) => updateField("admission_no", e.target.value)} placeholder="ADM-XXXX" className={errors.admission_no ? "border-destructive" : ""} />
                    {errors.admission_no && <p className="text-xs text-destructive mt-1">{errors.admission_no}</p>}
                  </div>
                  <div>
                    <Label>Roll Number</Label>
                    <Input value={form.roll_no} onChange={(e) => updateField("roll_no", e.target.value)} placeholder="Auto-generated" />
                  </div>
                  <div>
                    <Label>Class <span className="text-destructive">*</span></Label>
                    <Select value={form.class} onValueChange={(v) => updateField("class", v)}>
                      <SelectTrigger className={errors.class ? "border-destructive" : ""}><SelectValue placeholder="Select class" /></SelectTrigger>
                      <SelectContent>
                        {CLASSES.map((c) => <SelectItem key={c} value={c}>Class {c}</SelectItem>)}
                      </SelectContent>
                    </Select>
                    {errors.class && <p className="text-xs text-destructive mt-1">{errors.class}</p>}
                  </div>
                  <div>
                    <Label>Section <span className="text-destructive">*</span></Label>
                    <Select value={form.section} onValueChange={(v) => updateField("section", v)}>
                      <SelectTrigger className={errors.section ? "border-destructive" : ""}><SelectValue placeholder="Select section" /></SelectTrigger>
                      <SelectContent>
                        {SECTIONS.map((s) => <SelectItem key={s} value={s}>Section {s}</SelectItem>)}
                      </SelectContent>
                    </Select>
                    {errors.section && <p className="text-xs text-destructive mt-1">{errors.section}</p>}
                  </div>
                  <div>
                    <Label>Academic Year</Label>
                    <Select value={form.academic_year} onValueChange={(v) => updateField("academic_year", v)}>
                      <SelectTrigger><SelectValue /></SelectTrigger>
                      <SelectContent>
                        {ACADEMIC_YEARS.map((y) => <SelectItem key={y} value={y}>{y}</SelectItem>)}
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
                        <SelectItem value="alumni">Alumni</SelectItem>
                      </SelectContent>
                    </Select>
                  </div>
                </div>
              </CardContent>
            </Card>

            {/* Parent / Guardian */}
            <Card>
              <CardHeader><CardTitle className="text-lg">Parent / Guardian Details</CardTitle></CardHeader>
              <CardContent>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <Label>Father's Name <span className="text-destructive">*</span></Label>
                    <Input value={form.father_name} onChange={(e) => updateField("father_name", e.target.value)} placeholder="Father's full name" className={errors.father_name ? "border-destructive" : ""} />
                    {errors.father_name && <p className="text-xs text-destructive mt-1">{errors.father_name}</p>}
                  </div>
                  <div>
                    <Label>Mother's Name</Label>
                    <Input value={form.mother_name} onChange={(e) => updateField("mother_name", e.target.value)} placeholder="Mother's full name" />
                  </div>
                  <div>
                    <Label>Primary Phone <span className="text-destructive">*</span></Label>
                    <Input value={form.primary_phone} onChange={(e) => updateField("primary_phone", e.target.value)} placeholder="10-digit phone number" className={errors.primary_phone ? "border-destructive" : ""} />
                    {errors.primary_phone && <p className="text-xs text-destructive mt-1">{errors.primary_phone}</p>}
                  </div>
                  <div>
                    <Label>WhatsApp Number <span className="text-destructive">*</span></Label>
                    <Input value={form.whatsapp_number} onChange={(e) => updateField("whatsapp_number", e.target.value)} placeholder="WhatsApp number" className={errors.whatsapp_number ? "border-destructive" : ""} />
                    {errors.whatsapp_number && <p className="text-xs text-destructive mt-1">{errors.whatsapp_number}</p>}
                  </div>
                  <div>
                    <Label>Email</Label>
                    <Input type="email" value={form.email} onChange={(e) => updateField("email", e.target.value)} placeholder="Parent email (optional)" />
                  </div>
                  <div>
                    <Label>Emergency Contact</Label>
                    <Input value={form.emergency_contact} onChange={(e) => updateField("emergency_contact", e.target.value)} placeholder="Emergency phone number" />
                  </div>
                  <div className="sm:col-span-2">
                    <Label>Address <span className="text-destructive">*</span></Label>
                    <Textarea value={form.address} onChange={(e) => updateField("address", e.target.value)} placeholder="Full residential address" className={errors.address ? "border-destructive" : ""} rows={3} />
                    {errors.address && <p className="text-xs text-destructive mt-1">{errors.address}</p>}
                  </div>
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
                    {saving ? <><Loader2 className="h-4 w-4 mr-2 animate-spin" /> Saving...</> : <><Save className="h-4 w-4 mr-2" /> {isEdit ? "Update Student" : "Save Student"}</>}
                  </Button>
                  <Button type="button" variant="outline" className="w-full" onClick={() => navigate("/admin/students")}>Cancel</Button>
                </CardContent>
              </Card>
              <Card>
                <CardContent className="pt-6">
                  <h4 className="font-medium text-sm mb-2">Quick Tips</h4>
                  <ul className="text-xs text-muted-foreground space-y-1.5">
                    <li>• Admission Number must be unique</li>
                    <li>• WhatsApp number is mandatory for messaging</li>
                    <li>• Roll number auto-generates if left empty</li>
                    <li>• Photo upload supports JPG/PNG (max 2MB)</li>
                  </ul>
                </CardContent>
              </Card>
            </div>
          </div>
        </div>
      </form>
    </div>
  );
}
