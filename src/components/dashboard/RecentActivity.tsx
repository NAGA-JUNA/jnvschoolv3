import { Card } from "@/components/ui/card";
import { ScrollArea } from "@/components/ui/scroll-area";
import { ActivityItem } from "@/types";
import { Bell, Image, UserPlus, GraduationCap, Calendar, Users } from "lucide-react";
import { Link } from "react-router-dom";
import { cn } from "@/lib/utils";

const iconMap = {
  notification: Bell,
  gallery: Image,
  admission: UserPlus,
  student: GraduationCap,
  teacher: Users,
  event: Calendar,
};

const colorMap = {
  notification: "text-kpi-blue bg-kpi-blue/10",
  gallery: "text-kpi-orange bg-kpi-orange/10",
  admission: "text-kpi-green bg-kpi-green/10",
  student: "text-kpi-purple bg-kpi-purple/10",
  teacher: "text-kpi-pink bg-kpi-pink/10",
  event: "text-info bg-info/10",
};

const placeholderActivity: ActivityItem[] = [
  { id: 1, type: "notification", message: "New notification submitted for approval", timestamp: "2 min ago", link: "/admin/notifications" },
  { id: 2, type: "admission", message: "New admission application received", timestamp: "15 min ago", link: "/admin/admissions" },
  { id: 3, type: "gallery", message: "3 new photos uploaded to Annual Day", timestamp: "1 hour ago", link: "/admin/gallery-approvals" },
  { id: 4, type: "student", message: "Student record updated: Rahul Sharma", timestamp: "2 hours ago", link: "/admin/students" },
  { id: 5, type: "event", message: "Parent-Teacher meeting scheduled", timestamp: "3 hours ago", link: "/admin/events" },
  { id: 6, type: "teacher", message: "New teacher onboarded: Priya Singh", timestamp: "5 hours ago", link: "/admin/teachers" },
];

interface RecentActivityProps {
  items?: ActivityItem[];
}

export function RecentActivity({ items = placeholderActivity }: RecentActivityProps) {
  return (
    <Card className="p-5">
      <h3 className="font-semibold mb-4">Recent Activity</h3>
      <ScrollArea className="h-[320px]">
        <div className="space-y-3">
          {items.map((item) => {
            const Icon = iconMap[item.type];
            return (
              <Link
                key={item.id}
                to={item.link || "#"}
                className="flex items-start gap-3 p-3 rounded-lg hover:bg-muted/50 transition-colors group"
              >
                <div className={cn("p-2 rounded-lg flex-shrink-0", colorMap[item.type])}>
                  <Icon className="h-4 w-4" />
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium group-hover:text-primary transition-colors">
                    {item.message}
                  </p>
                  <p className="text-xs text-muted-foreground mt-1">{item.timestamp}</p>
                </div>
              </Link>
            );
          })}
        </div>
      </ScrollArea>
    </Card>
  );
}
