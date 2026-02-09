import { useState } from "react";
import { PageHeader } from "@/components/shared/PageHeader";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Search } from "lucide-react";
import { AuditLog } from "@/types";

const mockLogs: AuditLog[] = [
  { id: 1, user_id: 1, user_name: "Admin User", action: "approved", target_type: "notification", target_id: 1, details: "Approved notification: Annual Day Registration", ip_address: "192.168.1.100", created_at: "2024-03-01 14:30:00" },
  { id: 2, user_id: 1, user_name: "Admin User", action: "created", target_type: "teacher", target_id: 3, details: "Created teacher account: Anita Desai", ip_address: "192.168.1.100", created_at: "2024-03-01 10:15:00" },
  { id: 3, user_id: 1, user_name: "Admin User", action: "rejected", target_type: "gallery_item", target_id: 5, details: "Rejected gallery upload", ip_address: "192.168.1.100", created_at: "2024-02-28 16:45:00" },
  { id: 4, user_id: 2, user_name: "Priya Singh", action: "submitted", target_type: "notification", target_id: 3, details: "Submitted notification: Emergency Water Supply", ip_address: "192.168.1.101", created_at: "2024-02-28 09:00:00" },
  { id: 5, user_id: 1, user_name: "Admin User", action: "email_created", target_type: "email", target_id: 2, details: "Created email: rajesh.kumar@school.com", ip_address: "192.168.1.100", created_at: "2024-02-10 11:20:00" },
];

export default function AuditLogsPage() {
  const [search, setSearch] = useState("");
  const [actionFilter, setActionFilter] = useState("all");

  const filtered = mockLogs.filter((l) => {
    const matchSearch = l.details?.toLowerCase().includes(search.toLowerCase()) || l.user_name.toLowerCase().includes(search.toLowerCase());
    const matchAction = actionFilter === "all" || l.action === actionFilter;
    return matchSearch && matchAction;
  });

  return (
    <div className="space-y-6">
      <PageHeader title="Audit Logs" description="Track all critical actions in the system" />

      <Card>
        <div className="p-4 border-b flex flex-wrap gap-3">
          <div className="relative flex-1 min-w-[200px]">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input placeholder="Search logs..." value={search} onChange={(e) => setSearch(e.target.value)} className="pl-9" />
          </div>
          <Select value={actionFilter} onValueChange={setActionFilter}>
            <SelectTrigger className="w-[150px]"><SelectValue placeholder="Action" /></SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Actions</SelectItem>
              <SelectItem value="created">Created</SelectItem>
              <SelectItem value="approved">Approved</SelectItem>
              <SelectItem value="rejected">Rejected</SelectItem>
              <SelectItem value="submitted">Submitted</SelectItem>
              <SelectItem value="email_created">Email Created</SelectItem>
            </SelectContent>
          </Select>
        </div>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Timestamp</TableHead>
              <TableHead>User</TableHead>
              <TableHead>Action</TableHead>
              <TableHead>Details</TableHead>
              <TableHead>IP Address</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {filtered.map((log) => (
              <TableRow key={log.id}>
                <TableCell className="text-xs text-muted-foreground whitespace-nowrap">{log.created_at}</TableCell>
                <TableCell className="font-medium">{log.user_name}</TableCell>
                <TableCell className="capitalize">{log.action.replace("_", " ")}</TableCell>
                <TableCell className="max-w-[300px] truncate">{log.details}</TableCell>
                <TableCell className="text-xs font-mono text-muted-foreground">{log.ip_address}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Card>
    </div>
  );
}
