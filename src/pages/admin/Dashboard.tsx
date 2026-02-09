import { Users, GraduationCap, Bell, Image, UserPlus } from "lucide-react";
import { KPICard } from "@/components/dashboard/KPICard";
import { AlertBanner } from "@/components/dashboard/AlertBanner";
import { CalendarWidget } from "@/components/dashboard/CalendarWidget";
import { TrendChart } from "@/components/dashboard/TrendChart";
import { RecentActivity } from "@/components/dashboard/RecentActivity";
import { QuickActions } from "@/components/dashboard/QuickActions";
import { ErrorState } from "@/components/shared/ErrorState";
import { Skeleton } from "@/components/ui/skeleton";
import { useApi } from "@/hooks/useApi";
import api from "@/api/client";
import { ADMIN } from "@/api/endpoints";

interface DashboardMetrics {
  total_students: number;
  total_teachers: number;
  pending_notifications: number;
  pending_gallery: number;
  pending_admissions: number;
  upcoming_events: number;
  total_alumni: number;
}

interface AlertItem {
  type: "info" | "warning" | "error";
  message: string;
  link?: string;
}

interface ActivityItem {
  id: number;
  action: string;
  entity_type: string;
  entity_id: number;
  created_at: string;
  user_name: string;
  user_role: string;
}

interface TrendDataPoint {
  month: string;
  admissions: number;
  notifications: number;
  gallery: number;
}

export default function AdminDashboard() {
  const { data: metrics, loading: metricsLoading, error: metricsError, refetch: refetchMetrics } = useApi<DashboardMetrics>(
    () => api.get<DashboardMetrics>(ADMIN.dashboard)
  );
  const { data: alerts, loading: alertsLoading } = useApi<AlertItem[]>(
    () => api.get<AlertItem[]>(ADMIN.alerts)
  );
  const { data: activity, loading: activityLoading } = useApi<ActivityItem[]>(
    () => api.get<ActivityItem[]>(ADMIN.activity)
  );
  const { data: trends, loading: trendsLoading } = useApi<TrendDataPoint[]>(
    () => api.get<TrendDataPoint[]>("/admin/dashboard/trends")
  );

  if (metricsError) {
    return <ErrorState message={metricsError} onRetry={refetchMetrics} />;
  }

  return (
    <div className="space-y-6">
      {/* KPI Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        {metricsLoading ? (
          Array.from({ length: 5 }).map((_, i) => (
            <Skeleton key={i} className="h-28 rounded-xl" />
          ))
        ) : (
          <>
            <KPICard title="Total Students" value={metrics?.total_students ?? 0} icon={GraduationCap} color="blue" />
            <KPICard title="Total Teachers" value={metrics?.total_teachers ?? 0} icon={Users} color="green" />
            <KPICard title="Pending Notifications" value={metrics?.pending_notifications ?? 0} icon={Bell} color="orange" />
            <KPICard title="Pending Gallery" value={metrics?.pending_gallery ?? 0} icon={Image} color="purple" />
            <KPICard title="New Admissions" value={metrics?.pending_admissions ?? 0} icon={UserPlus} color="pink" />
          </>
        )}
      </div>

      {/* Alert Banner */}
      {!alertsLoading && alerts && alerts.length > 0 && (
        <AlertBanner message={alerts[0].message} type={alerts[0].type} />
      )}

      {/* Main dashboard grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {/* Left content */}
        <div className="lg:col-span-8 space-y-6">
          <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <TrendChart data={trends ?? undefined} loading={trendsLoading} />
            <CalendarWidget />
          </div>
          <RecentActivity items={activity ?? undefined} loading={activityLoading} />
        </div>

        {/* Right panel */}
        <div className="lg:col-span-4 space-y-6">
          <QuickActions role="admin" />
        </div>
      </div>
    </div>
  );
}
