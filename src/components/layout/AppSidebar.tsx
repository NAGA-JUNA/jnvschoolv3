import { Link, useLocation } from "react-router-dom";
import {
  LayoutDashboard, Users, GraduationCap, UserPlus, Bell, Image, Calendar,
  MessageCircle, Mail, BarChart3, FileText, Settings, LogOut, GraduationCapIcon,
  ChevronLeft, Menu,
} from "lucide-react";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { ScrollArea } from "@/components/ui/scroll-area";
import { useState } from "react";

const adminLinks = [
  { label: "Dashboard", icon: LayoutDashboard, href: "/admin" },
  { label: "Teachers", icon: Users, href: "/admin/teachers" },
  { label: "Students", icon: GraduationCap, href: "/admin/students" },
  { label: "Admissions", icon: UserPlus, href: "/admin/admissions" },
  { label: "Notifications", icon: Bell, href: "/admin/notifications" },
  { label: "Gallery Categories", icon: Image, href: "/admin/gallery-categories" },
  { label: "Gallery Approvals", icon: Image, href: "/admin/gallery-approvals" },
  { label: "Events", icon: Calendar, href: "/admin/events" },
  { label: "WhatsApp", icon: MessageCircle, href: "/admin/whatsapp" },
  { label: "Email Management", icon: Mail, href: "/admin/emails" },
  { label: "Reports", icon: BarChart3, href: "/admin/reports" },
  { label: "Audit Logs", icon: FileText, href: "/admin/audit" },
  { label: "Settings", icon: Settings, href: "/admin/settings" },
];

const teacherLinks = [
  { label: "Dashboard", icon: LayoutDashboard, href: "/teacher" },
  { label: "Post Notification", icon: Bell, href: "/teacher/post-notification" },
  { label: "Upload Gallery", icon: Image, href: "/teacher/upload-gallery" },
  { label: "My Submissions", icon: FileText, href: "/teacher/submissions" },
  { label: "Profile", icon: Users, href: "/teacher/profile" },
];

interface AppSidebarProps {
  role: "admin" | "teacher";
}

export function AppSidebar({ role }: AppSidebarProps) {
  const location = useLocation();
  const [collapsed, setCollapsed] = useState(false);
  const links = role === "admin" ? adminLinks : teacherLinks;

  return (
    <>
      {/* Mobile overlay */}
      <div className={cn(
        "lg:hidden fixed inset-0 bg-black/50 z-40 transition-opacity",
        collapsed ? "opacity-0 pointer-events-none" : "opacity-100"
      )} onClick={() => setCollapsed(true)} />

      {/* Mobile toggle */}
      <Button
        variant="ghost"
        size="icon"
        className="lg:hidden fixed top-3 left-3 z-50"
        onClick={() => setCollapsed(!collapsed)}
      >
        <Menu className="h-5 w-5" />
      </Button>

      <aside className={cn(
        "fixed lg:sticky top-0 left-0 z-40 h-screen flex flex-col bg-sidebar text-sidebar-foreground transition-all duration-300",
        collapsed ? "-translate-x-full lg:translate-x-0 lg:w-16" : "w-64 translate-x-0"
      )}>
        {/* Header */}
        <div className="flex items-center gap-3 px-4 h-16 border-b border-sidebar-border">
          <div className="bg-sidebar-primary rounded-lg p-2">
            <GraduationCapIcon className="h-5 w-5 text-sidebar-primary-foreground" />
          </div>
          {!collapsed && (
            <div className="flex-1 min-w-0">
              <h2 className="font-bold text-sm truncate">SchoolAdmin</h2>
              <p className="text-xs text-sidebar-foreground/60 capitalize">{role} Panel</p>
            </div>
          )}
          <Button
            variant="ghost"
            size="icon"
            className="hidden lg:flex h-8 w-8 text-sidebar-foreground/60 hover:text-sidebar-foreground hover:bg-sidebar-accent"
            onClick={() => setCollapsed(!collapsed)}
          >
            <ChevronLeft className={cn("h-4 w-4 transition-transform", collapsed && "rotate-180")} />
          </Button>
        </div>

        {/* Navigation */}
        <ScrollArea className="flex-1 py-4">
          <nav className="px-2 space-y-1">
            {links.map((link) => {
              const isActive = location.pathname === link.href ||
                (link.href !== "/admin" && link.href !== "/teacher" && location.pathname.startsWith(link.href));
              return (
                <Link
                  key={link.href}
                  to={link.href}
                  onClick={() => window.innerWidth < 1024 && setCollapsed(true)}
                  className={cn(
                    "flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors",
                    isActive
                      ? "bg-sidebar-primary text-sidebar-primary-foreground"
                      : "text-sidebar-foreground/70 hover:text-sidebar-foreground hover:bg-sidebar-accent"
                  )}
                >
                  <link.icon className="h-4 w-4 flex-shrink-0" />
                  {!collapsed && <span className="truncate">{link.label}</span>}
                </Link>
              );
            })}
          </nav>
        </ScrollArea>

        {/* Footer */}
        <div className="border-t border-sidebar-border p-3">
          <Link
            to="/"
            className="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-sidebar-foreground/70 hover:text-sidebar-foreground hover:bg-sidebar-accent transition-colors"
          >
            <LogOut className="h-4 w-4 flex-shrink-0" />
            {!collapsed && <span>Sign Out</span>}
          </Link>
        </div>
      </aside>
    </>
  );
}
