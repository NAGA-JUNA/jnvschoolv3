import { Users, GraduationCap, Bell, Image, UserPlus } from "lucide-react";
import { KPICard } from "@/components/dashboard/KPICard";
import { AlertBanner } from "@/components/dashboard/AlertBanner";
import { CalendarWidget } from "@/components/dashboard/CalendarWidget";
import { TrendChart } from "@/components/dashboard/TrendChart";
import { RecentActivity } from "@/components/dashboard/RecentActivity";
import { QuickActions } from "@/components/dashboard/QuickActions";

export default function AdminDashboard() {
  return (
    <div className="space-y-6 pl-0 lg:pl-0">
      <h1 className="text-2xl font-bold tracking-tight">Admin Dashboard</h1>

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
        type="info"
      />

      {/* 3-column dashboard layout */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {/* Main content */}
        <div className="lg:col-span-8 space-y-6">
          <TrendChart />
          <RecentActivity />
        </div>
        {/* Right panel */}
        <div className="lg:col-span-4 space-y-6">
          <QuickActions role="admin" />
          <CalendarWidget />
        </div>
      </div>
    </div>
  );
}
