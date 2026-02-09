import { PageHeader } from "@/components/shared/PageHeader";
import { StatusBadge } from "@/components/shared/StatusBadge";
import { Card } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";

const mockSubmissions = [
  { id: 1, type: "notification", title: "Annual Day Registration", status: "approved", submitted_at: "2024-03-01" },
  { id: 2, type: "notification", title: "Emergency: Water Supply", status: "pending", submitted_at: "2024-03-04" },
  { id: 3, type: "gallery", title: "Sports Day Photos (5 images)", status: "pending", submitted_at: "2024-03-02" },
  { id: 4, type: "gallery", title: "Annual Day Highlights (YouTube)", status: "rejected", submitted_at: "2024-02-28" },
];

export default function MySubmissions() {
  return (
    <div className="space-y-6">
      <PageHeader title="My Submissions" description="Track your notification and gallery submissions" />

      <Card>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Title</TableHead>
              <TableHead>Type</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Submitted</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {mockSubmissions.map((s) => (
              <TableRow key={s.id}>
                <TableCell className="font-medium">{s.title}</TableCell>
                <TableCell><Badge variant="outline" className="capitalize">{s.type}</Badge></TableCell>
                <TableCell><StatusBadge status={s.status} /></TableCell>
                <TableCell>{s.submitted_at}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Card>
    </div>
  );
}
