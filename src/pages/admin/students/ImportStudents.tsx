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
  admission_no: string;
  class: string;
  section: string;
  status: "valid" | "duplicate" | "error";
  message?: string;
}

export default function ImportStudents() {
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
      { row: 1, full_name: "New Student 1", admission_no: "ADM-2001", class: "10", section: "A", status: "valid" },
      { row: 2, full_name: "New Student 2", admission_no: "ADM-2002", class: "10", section: "B", status: "valid" },
      { row: 3, full_name: "Rahul Sharma", admission_no: "ADM-1001", class: "10", section: "A", status: "duplicate", message: "Admission number already exists" },
      { row: 4, full_name: "", admission_no: "ADM-2003", class: "10", section: "A", status: "error", message: "Full name is required" },
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
    toast.success(`${validRows.length} students imported successfully!`);
    navigate("/admin/students");
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
        title="Import Students"
        description="Upload an Excel file to bulk import student records"
        action={
          <Button variant="outline" onClick={() => navigate("/admin/students")}>
            <ArrowLeft className="h-4 w-4 mr-2" /> Back to Students
          </Button>
        }
      />

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          {/* Upload Area */}
          <Card>
            <CardHeader>
              <CardTitle className="text-lg">Upload File</CardTitle>
              <CardDescription>Upload an Excel (.xlsx) or CSV file with student data</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="border-2 border-dashed border-border rounded-xl p-8 text-center hover:border-primary/50 transition-colors">
                <FileSpreadsheet className="h-12 w-12 mx-auto text-muted-foreground mb-3" />
                <Label htmlFor="file-upload" className="cursor-pointer">
                  <span className="text-primary font-medium hover:underline">Click to upload</span>
                  <span className="text-muted-foreground"> or drag and drop</span>
                </Label>
                <Input id="file-upload" type="file" accept=".xlsx,.xls,.csv" onChange={handleFileChange} className="hidden" />
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
                      <TableHead>Admission No</TableHead>
                      <TableHead>Class</TableHead>
                      <TableHead>Section</TableHead>
                      <TableHead>Status</TableHead>
                      <TableHead>Message</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    {preview.map((r) => (
                      <TableRow key={r.row} className={r.status !== "valid" ? "bg-destructive/5" : ""}>
                        <TableCell>{r.row}</TableCell>
                        <TableCell className="font-medium">{r.full_name || "—"}</TableCell>
                        <TableCell className="font-mono text-sm">{r.admission_no}</TableCell>
                        <TableCell>{r.class}</TableCell>
                        <TableCell>{r.section}</TableCell>
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
                {importing ? <><Loader2 className="h-4 w-4 mr-2 animate-spin" /> Importing...</> : <><Upload className="h-4 w-4 mr-2" /> Import {validCount} Students</>}
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
                <li>• Admission Number (required, unique)</li>
                <li>• Class (required)</li>
                <li>• Section (required)</li>
                <li>• Gender</li>
                <li>• Date of Birth</li>
                <li>• Father's Name</li>
                <li>• Mother's Name</li>
                <li>• Phone Number</li>
                <li>• WhatsApp Number (required)</li>
                <li>• Email</li>
                <li>• Address</li>
              </ul>
            </CardContent>
          </Card>
          <Card>
            <CardContent className="pt-6">
              <h4 className="font-medium text-sm mb-2">Import Rules</h4>
              <ul className="text-xs text-muted-foreground space-y-1.5">
                <li>• Duplicates are checked by Admission Number</li>
                <li>• Invalid rows will be skipped</li>
                <li>• Max 500 records per import</li>
                <li>• Existing records will NOT be overwritten</li>
              </ul>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
}
