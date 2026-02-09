import { Card } from "@/components/ui/card";
import { Bell, Image, MessageCircle, UserPlus, GraduationCap, Mail, BarChart3, LucideIcon } from "lucide-react";
import { Link } from "react-router-dom";
import { cn } from "@/lib/utils";

interface QuickAction {
  label: string;
  icon: LucideIcon;
  href: string;
  iconBg: string;
  iconColor: string;
}

const adminActions: QuickAction[] = [
  { label: "New Announcement", icon: Bell, href: "/admin/notifications?new=1", iconBg: "bg-kpi-blue/10", iconColor: "text-kpi-blue" },
  { label: "Send Message", icon: MessageCircle, href: "/admin/whatsapp", iconBg: "bg-kpi-green/10", iconColor: "text-kpi-green" },
  { label: "View Report", icon: BarChart3, href: "/admin/reports", iconBg: "bg-kpi-orange/10", iconColor: "text-kpi-orange" },
  { label: "Add Teacher/Staff", icon: UserPlus, href: "/admin/teachers?new=1", iconBg: "bg-kpi-purple/10", iconColor: "text-kpi-purple" },
  { label: "Admit Students", icon: GraduationCap, href: "/admin/students?new=1", iconBg: "bg-kpi-pink/10", iconColor: "text-kpi-pink" },
  { label: "Create Email", icon: Mail, href: "/admin/emails", iconBg: "bg-info/10", iconColor: "text-info" },
];

const teacherActions: QuickAction[] = [
  { label: "Post Notification", icon: Bell, href: "/teacher/post-notification", iconBg: "bg-kpi-blue/10", iconColor: "text-kpi-blue" },
  { label: "Upload Gallery", icon: Image, href: "/teacher/upload-gallery", iconBg: "bg-kpi-orange/10", iconColor: "text-kpi-orange" },
];

interface QuickActionsProps {
  role?: "admin" | "teacher";
}

export function QuickActions({ role = "admin" }: QuickActionsProps) {
  const actions = role === "admin" ? adminActions : teacherActions;

  return (
    <Card className="p-5">
      <h3 className="font-semibold mb-4">Quick Actions</h3>
      <div className="space-y-2">
        {actions.map((action) => (
          <Link
            key={action.label}
            to={action.href}
            className="flex items-center gap-3 p-3 rounded-xl border border-border hover:border-primary/30 hover:shadow-sm transition-all group"
          >
            <div className={cn("h-9 w-9 rounded-lg flex items-center justify-center flex-shrink-0", action.iconBg)}>
              <action.icon className={cn("h-4 w-4", action.iconColor)} />
            </div>
            <span className="text-sm font-medium text-foreground group-hover:text-primary transition-colors">
              {action.label}
            </span>
          </Link>
        ))}
      </div>
    </Card>
  );
}
