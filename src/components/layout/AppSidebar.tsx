import { Link, useLocation } from "react-router-dom";
import jnvLogo from "@/assets/jnvtech-logo.png";
import {
  LayoutDashboard, Users, GraduationCap, UserPlus, Bell, Image, Calendar,
  MessageCircle, Mail, BarChart3, FileText, Settings, LogOut,
  ChevronLeft, ChevronDown, ChevronRight, BookOpen, Handshake, ShieldCheck, Palette,
  ClipboardList, UserCheck, Upload, School,
} from "lucide-react";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { ScrollArea } from "@/components/ui/scroll-area";
import { useState } from "react";
import { LucideIcon } from "lucide-react";
import { useTheme } from "@/contexts/ThemeContext";

interface NavItem {
  label: string;
  icon: LucideIcon;
  href: string;
}

interface NavGroup {
  title: string;
  icon: LucideIcon;
  items: NavItem[];
}

const adminGroups: NavGroup[] = [
  {
    title: "Academics",
    icon: BookOpen,
    items: [
      { label: "Dashboard", icon: LayoutDashboard, href: "/admin" },
      { label: "Students", icon: GraduationCap, href: "/admin/students" },
      { label: "Add Student", icon: UserPlus, href: "/admin/students/add" },
      { label: "Alumni", icon: School, href: "/admin/students/alumni" },
      { label: "Import Students", icon: Upload, href: "/admin/students/import" },
      { label: "Teachers", icon: Users, href: "/admin/teachers" },
      { label: "Admissions", icon: ClipboardList, href: "/admin/admissions" },
    ],
  },
  {
    title: "Collaboration",
    icon: Handshake,
    items: [
      { label: "Notifications", icon: Bell, href: "/admin/notifications" },
      { label: "Gallery", icon: Image, href: "/admin/gallery-categories" },
      { label: "Gallery Approvals", icon: Image, href: "/admin/gallery-approvals" },
      { label: "Events", icon: Calendar, href: "/admin/events" },
    ],
  },
  {
    title: "Communication",
    icon: MessageCircle,
    items: [
      { label: "WhatsApp", icon: MessageCircle, href: "/admin/whatsapp" },
      { label: "Email Management", icon: Mail, href: "/admin/emails" },
    ],
  },
  {
    title: "Administration",
    icon: ShieldCheck,
    items: [
      { label: "Reports", icon: BarChart3, href: "/admin/reports" },
      { label: "Audit Logs", icon: FileText, href: "/admin/audit" },
      { label: "Branding", icon: Palette, href: "/admin/branding" },
      { label: "Settings", icon: Settings, href: "/admin/settings" },
    ],
  },
];

const teacherLinks: NavItem[] = [
  { label: "Dashboard", icon: LayoutDashboard, href: "/teacher" },
  { label: "Post Notification", icon: Bell, href: "/teacher/post-notification" },
  { label: "Upload Gallery", icon: Image, href: "/teacher/upload-gallery" },
  { label: "My Submissions", icon: FileText, href: "/teacher/submissions" },
  { label: "Profile", icon: Users, href: "/teacher/profile" },
];

interface AppSidebarProps {
  role: "admin" | "teacher";
  collapsed: boolean;
  onToggle: () => void;
}

export function AppSidebar({ role, collapsed, onToggle }: AppSidebarProps) {
  const location = useLocation();
  const { branding } = useTheme();
  const [openGroups, setOpenGroups] = useState<Record<string, boolean>>({ Academics: true, Collaboration: true });

  const toggleGroup = (title: string) => {
    setOpenGroups((prev) => ({ ...prev, [title]: !prev[title] }));
  };

  const isActive = (href: string) =>
    location.pathname === href ||
    (href !== "/admin" && href !== "/teacher" && location.pathname.startsWith(href));

  const renderLink = (link: NavItem) => (
    <Link
      key={link.href}
      to={link.href}
      onClick={() => window.innerWidth < 1024 && onToggle()}
      className={cn(
        "flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200",
        isActive(link.href)
          ? "bg-sidebar-primary text-sidebar-primary-foreground shadow-md shadow-sidebar-primary/25"
          : "text-sidebar-foreground/70 hover:text-sidebar-foreground hover:bg-sidebar-accent"
      )}
    >
      <link.icon className="h-4 w-4 flex-shrink-0" />
      {!collapsed && <span className="truncate">{link.label}</span>}
    </Link>
  );

  return (
    <>
      {/* Mobile overlay */}
      <div
        className={cn(
          "lg:hidden fixed inset-0 bg-black/50 z-40 transition-opacity",
          collapsed ? "opacity-0 pointer-events-none" : "opacity-100"
        )}
        onClick={onToggle}
      />

      <aside
        className={cn(
          "fixed lg:sticky top-0 left-0 z-40 h-screen flex flex-col transition-all duration-300",
          "bg-gradient-to-b from-[hsl(var(--sidebar-gradient-from))] to-[hsl(var(--sidebar-gradient-to))]",
          "text-sidebar-foreground",
          collapsed ? "-translate-x-full lg:translate-x-0 lg:w-16" : "w-64 translate-x-0"
        )}
      >
        {/* Logo & Branding Header */}
        <div className="border-b border-sidebar-border/50">
          {/* Logo Zone */}
          <div className={cn(
            "flex items-center justify-center py-5 px-3",
            collapsed ? "py-3" : "py-5"
          )}>
            {branding.logoUrl ? (
              <img
                src={branding.logoUrl}
                alt={branding.schoolName}
                className={cn(
                  "object-contain transition-all duration-300",
                  collapsed ? "w-10 h-10" : "w-20 h-20"
                )}
              />
            ) : (
              <div className={cn(
                "bg-sidebar-primary/20 rounded-2xl flex items-center justify-center transition-all duration-300",
                collapsed ? "w-10 h-10" : "w-20 h-20"
              )}>
                <GraduationCap className={cn(
                  "text-sidebar-primary-foreground transition-all",
                  collapsed ? "h-5 w-5" : "h-10 w-10"
                )} />
              </div>
            )}
          </div>
          {/* Name & toggle */}
          <div className="flex items-center gap-2 px-4 pb-3">
            {!collapsed && (
              <div className="flex-1 min-w-0 text-center">
                <h2 className="font-bold text-sm text-sidebar-foreground truncate">{branding.schoolName}</h2>
                <p className="text-[10px] text-sidebar-foreground/50 uppercase tracking-wider capitalize">{role} Panel</p>
              </div>
            )}
            <Button
              variant="ghost"
              size="icon"
              className="hidden lg:flex h-7 w-7 text-sidebar-foreground/50 hover:text-sidebar-foreground hover:bg-sidebar-accent ml-auto"
              onClick={onToggle}
            >
              <ChevronLeft className={cn("h-4 w-4 transition-transform", collapsed && "rotate-180")} />
            </Button>
          </div>
        </div>

        {/* Navigation */}
        <ScrollArea className="flex-1 py-3">
          <nav className="px-2 space-y-1">
            {role === "admin"
              ? adminGroups.map((group) => (
                  <div key={group.title} className="mb-1">
                    {!collapsed && (
                      <button
                        onClick={() => toggleGroup(group.title)}
                        className="flex items-center justify-between w-full px-3 py-2 text-[11px] uppercase tracking-wider font-semibold text-sidebar-foreground/40 hover:text-sidebar-foreground/60 transition-colors"
                      >
                        <span>{group.title}</span>
                        {openGroups[group.title] ? (
                          <ChevronDown className="h-3 w-3" />
                        ) : (
                          <ChevronRight className="h-3 w-3" />
                        )}
                      </button>
                    )}
                    {(collapsed || openGroups[group.title]) && (
                      <div className="space-y-0.5">
                        {group.items.map(renderLink)}
                      </div>
                    )}
                  </div>
                ))
              : teacherLinks.map(renderLink)}
          </nav>
        </ScrollArea>

        {/* Footer */}
        <div className="border-t border-sidebar-border/50 p-2">
          <Link
            to="/"
            className="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-sidebar-foreground/60 hover:text-sidebar-foreground hover:bg-sidebar-accent transition-colors"
          >
            <LogOut className="h-4 w-4 flex-shrink-0" />
            {!collapsed && <span>Sign Out</span>}
          </Link>
          {!collapsed && (
            <div className="px-3 pt-3 pb-2 flex flex-col items-center gap-1">
              <Link
                to={`/${role}/developer`}
                className="inline-flex flex-col items-center gap-1 text-sidebar-foreground/30 hover:text-sidebar-foreground/60 transition-colors"
              >
                <img src={jnvLogo} alt="JNV Tech" className="h-8 w-auto object-contain opacity-60" />
                <span className="text-[10px]">Powered by JNV Tech</span>
              </Link>
            </div>
          )}
        </div>
      </aside>
    </>
  );
}
