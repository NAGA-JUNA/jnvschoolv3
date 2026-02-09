import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { PageHeader } from "@/components/shared/PageHeader";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { EmptyState } from "@/components/shared/EmptyState";
import { ArrowLeft, Upload, Download, FileSpreadsheet, CheckCircle, XCircle, AlertTriangle, Loader2 } from "lucide-react";
import { toast } from "sonner";

interface ImportRow {
  row: number;
  full_name: string;
  employee_id: string;
  subject: string;
  phone: string;
  status: "valid" | "duplicate" | "error";
  message?: string;
}

export default function ImportTeachers() {
  const navigate = useNavigate();
  const [file, setFile] = useState<File | null>(null);
  const [importing, setImporting] = useState(false);
  const [preview, setPreview] = useState<ImportRow[]>([]);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const f = e.target.files?.[0];
    if (!f) return;
    if (!f.name.endsWith(".xlsx") && !f.name.endsWith(".xls") && !f.name.endsWith(".csv")) {
      toast.error("Please upload an Excel (.xlsx, .xls) or CSV file");
      return;
    }
    setFile(f);
    // Simulate parsing
    setPreview([
      { row: 1, full_name: "New Teacher 1", employee_id: "EMP-011", subject: "Mathematics", phone: "9876543230", status: "valid" },
      { row: 2, full_name: "New Teacher 2", employee_id: "EMP-012", subject: "English", phone: "9876543231", status: "valid" },
      { row: 3, full_name: "Priya Singh", employee_id: "EMP-001", subject: "Mathematics", phone: "9876543210", status: "duplicate", message: "Employee ID already exists" },
      { row: 4, full_name: "", employee_id: "EMP-013", subject: "Science", phone: "9876543232", status: "error", message: "Full name is required" },
    ]);
  };

  const handleImport = async () => {
    const validRows = preview.filter((r) => r.status === "valid");
    if (validRows.length === 0) {
      toast.error("No valid records to import");
      return;
    }
    setImporting(true);
    await new Promise((r) => setTimeout(r, 2000));
    setImporting(false);
    toast.success(`${validRows.length} teachers imported successfully!`);
    navigate("/admin/teachers");
  };

  const handleDownloadTemplate = () => {
    toast.success("Downloading Excel template...");
  };

  const validCount = preview.filter((r) => r.status === "valid").length;
  const duplicateCount = preview.filter((r) => r.status === "duplicate").length;
  const errorCount = preview.filter((r) => r.status === "error").length;

  return (
    <div className="space-y-6">
      <PageHeader
        title="Import Teachers"
        description="Upload an Excel file to bulk import teacher records"
        action={
          <Button variant="outline" onClick={() => navigate("/admin/teachers")}>
            <ArrowLeft className="h-4 w-4 mr-2" /> Back to Teachers
          </Button>
        }
      />

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          {/* Upload Area */}
          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Upload File</CardTitle>
              <CardDescription>Upload an Excel (.xlsx) or CSV file with teacher data</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="border-2 border-dashed border-border rounded-xl p-8 text-center hover:border-primary/50 transition-colors">
                <FileSpreadsheet className="h-12 w-12 mx-auto text-muted-foreground mb-3" />
                <Label htmlFor="teacher-file-upload" className="cursor-pointer">
                  <span className="text-primary font-medium hover:underline">Click to upload</span>
                  <span className="text-muted-foreground"> or drag and drop</span>
                </Label>
                <Input id="teacher-file-upload" type="file" accept=".xlsx,.xls,.csv" onChange={handleFileChange} className="hidden" />
                <p className="text-xs text-muted-foreground mt-2">Supports .xlsx, .xls, .csv (max 5MB)</p>
                {file && (
                  <div className="mt-3 flex items-center justify-center gap-2 text-sm">
                    <FileSpreadsheet className="h-4 w-4 text-primary" />
                    <span className="font-medium">{file.name}</span>
                    <span className="text-muted-foreground">({(file.size / 1024).toFixed(1)} KB)</span>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>

          {/* Preview Table */}
          {preview.length > 0 && (
            <Card>
              <CardHeader>
                <CardTitle className="text-lg">Preview ({preview.length} rows)</CardTitle>
                <div className="flex gap-4 text-sm">
                  <span className="flex items-center gap-1.5" style={{ color: "hsl(var(--success))" }}><CheckCircle className="h-4 w-4" /> {validCount} Valid</span>
                  <span className="flex items-center gap-1.5 text-warning"><AlertTriangle className="h-4 w-4" /> {duplicateCount} Duplicates</span>
                  <span className="flex items-center gap-1.5 text-destructive"><XCircle className="h-4 w-4" /> {errorCount} Errors</span>
                </div>
              </CardHeader>
              <CardContent>
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Row</TableHead>
                      <TableHead>Name</TableHead>
                      <TableHead>Employee ID</TableHead>
                      <TableHead>Subject</TableHead>
                      <TableHead>Phone</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead>Message</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {preview.map((r) => (
                      <TableRow key={r.row} className={r.status !== "valid" ? "bg-destructive/5" : ""}>
                        <TableCell>{r.row}</TableCell>
                        <TableCell className="font-medium">{r.full_name || "—"}</TableCell>
                        <TableCell className="font-mono text-sm">{r.employee_id}</TableCell>
                        <TableCell>{r.subject}</TableCell>
                        <TableCell>{r.phone}</TableCell>
                        <TableCell><StatusBadge status={r.status === "valid" ? "active" : r.status === "duplicate" ? "pending" : "rejected"} /></TableCell>
                        <TableCell className="text-sm text-muted-foreground">{r.message || "—"}</TableCell>
                      </TableRow>
                    ))}
                  </TableBody>
                </Table>
              </CardContent>
            </Card>
          )}
        </div>

        {/* Sidebar */}
        <div className="space-y-4">
          <Card>
            <CardContent className="pt-6 space-y-4">
              <Button className="w-full" onClick={handleImport} disabled={importing || validCount === 0}>
                {importing ? <><Loader2 className="h-4 w-4 mr-2 animate-spin" /> Importing...</> : <><Upload className="h-4 w-4 mr-2" /> Import {validCount} Teachers</>}
              </Button>
              <Button variant="outline" className="w-full" onClick={handleDownloadTemplate}>
                <Download className="h-4 w-4 mr-2" /> Download Template
              </Button>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <h4 className="font-medium text-sm mb-2">Template Columns</h4>
              <ul className="text-xs text-muted-foreground space-y-1.5">
                <li>• Full Name (required)</li>
                <li>• Employee ID (required, unique)</li>
                <li>• Gender</li>
                <li>• Date of Birth</li>
                <li>• Phone Number (required)</li>
                <li>• WhatsApp Number (required)</li>
                <li>• Email (required)</li>
                <li>• Address</li>
                <li>• Qualification</li>
                <li>• Experience (years)</li>
                <li>• Joining Date</li>
                <li>• Subjects (comma-separated)</li>
                <li>• Employment Type</li>
              </ul>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <h4 className="font-medium text-sm mb-2">Import Rules</h4>
              <ul className="text-xs text-muted-foreground space-y-1.5">
                <li>• Duplicates are checked by Employee ID</li>
                <li>• Invalid rows will be skipped</li>
                <li>• Max 200 records per import</li>
                <li>• Existing records will NOT be overwritten</li>
              </ul>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
