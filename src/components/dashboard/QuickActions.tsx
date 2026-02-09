import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Bell, Image, MessageCircle, UserPlus, GraduationCap, Mail, LucideIcon } from "lucide-react";
import { Link } from "react-router-dom";

interface QuickAction {
  label: string;
  icon: LucideIcon;
  href: string;
  color: string;
}

const adminActions: QuickAction[] = [
  { label: "New Notification", icon: Bell, href: "/admin/notifications?new=1", color: "text-kpi-blue" },
  { label: "Upload Gallery", icon: Image, href: "/admin/gallery-approvals", color: "text-kpi-orange" },
  { label: "Open WhatsApp", icon: MessageCircle, href: "/admin/whatsapp", color: "text-kpi-green" },
  { label: "Add Teacher/Staff", icon: UserPlus, href: "/admin/teachers?new=1", color: "text-kpi-purple" },
  { label: "Add Student", icon: GraduationCap, href: "/admin/students?new=1", color: "text-kpi-pink" },
  { label: "Create Email", icon: Mail, href: "/admin/emails", color: "text-info" },
];

const teacherActions: QuickAction[] = [
  { label: "Post Notification", icon: Bell, href: "/teacher/post-notification", color: "text-kpi-blue" },
  { label: "Upload Gallery", icon: Image, href: "/teacher/upload-gallery", color: "text-kpi-orange" },
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
          <Button
            key={action.label}
            variant="ghost"
            className="w-full justify-start gap-3 h-11 hover:bg-muted/60"
            asChild
          >
            <Link to={action.href}>
              <action.icon className={`h-4 w-4 ${action.color}`} />
              <span className="text-sm">{action.label}</span>
            </Link>
          </Button>
        ))}
      </div>
    </Card>
  );
}
