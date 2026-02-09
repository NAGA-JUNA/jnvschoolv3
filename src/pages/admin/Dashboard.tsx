import { Users, GraduationCap, Bell, Image, UserPlus } from "lucide-react";
import { KPICard } from "@/components/dashboard/KPICard";
import { AlertBanner } from "@/components/dashboard/AlertBanner";
import { CalendarWidget } from "@/components/dashboard/CalendarWidget";
import { TrendChart } from "@/components/dashboard/TrendChart";
import { RecentActivity } from "@/components/dashboard/RecentActivity";
import { QuickActions } from "@/components/dashboard/QuickActions";

export default function AdminDashboard() {
  return (
    <div className="space-y-6">
      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <KPICard title="Total Students" value={1248} icon={GraduationCap} color="blue" trend={{ value: 12, label: "vs last month" }} />
        <KPICard title="Total Teachers" value={56} icon={Users} color="green" trend={{ value: 3, label: "vs last month" }} />
        <KPICard title="Pending Notifications" value={8} icon={Bell} color="orange" />
        <KPICard title="Pending Gallery" value={14} icon={Image} color="purple" />
        <KPICard title="New Admissions" value={23} icon={UserPlus} color="pink" trend={{ value: 18, label: "this week" }} />
      </div>

      {/* Alert Banner */}
      <AlertBanner
        message="School annual day preparations begin next week. Ensure all event registrations are complete."
        type="warning"
      />

      {/* Main dashboard grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {/* Left content */}
        <div className="lg:col-span-8 space-y-6">
          <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <TrendChart />
            <CalendarWidget />
          </div>
          <RecentActivity />
        </div>

        {/* Right panel */}
        <div className="lg:col-span-4 space-y-6">
          <QuickActions role="admin" />
        </div>
      </div>
    </div>
  );
}
