import { Bell, Image, FileText, Users } from "lucide-react";
import { KPICard } from "@/components/dashboard/KPICard";
import { CalendarWidget } from "@/components/dashboard/CalendarWidget";
import { RecentActivity } from "@/components/dashboard/RecentActivity";
import { QuickActions } from "@/components/dashboard/QuickActions";

export default function TeacherDashboard() {
  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold tracking-tight">Teacher Dashboard</h1>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <KPICard title="My Notifications" value={5} icon={Bell} color="blue" />
        <KPICard title="Gallery Uploads" value={12} icon={Image} color="orange" />
        <KPICard title="Pending Approvals" value={3} icon={FileText} color="purple" />
        <KPICard title="My Classes" value={4} icon={Users} color="green" />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div className="lg:col-span-8">
          <RecentActivity />
        </div>
        <div className="lg:col-span-4 space-y-6">
          <QuickActions role="teacher" />
          <CalendarWidget />
        </div>
      </div>
    </div>
  );
}
