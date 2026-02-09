import { Outlet } from "react-router-dom";
import { useState } from "react";
import { AppSidebar } from "@/components/layout/AppSidebar";
import { TopHeader } from "@/components/layout/TopHeader";
import { Footer } from "@/components/shared/Footer";

interface PanelLayoutProps {
  role: "admin" | "teacher";
}

export function PanelLayout({ role }: PanelLayoutProps) {
  const [collapsed, setCollapsed] = useState(true);

  return (
    <div className="flex min-h-screen w-full bg-background">
      <AppSidebar role={role} collapsed={collapsed} onToggle={() => setCollapsed(!collapsed)} />
      <div className="flex-1 flex flex-col min-w-0">
        <TopHeader onToggleSidebar={() => setCollapsed(!collapsed)} />
        <main className="flex-1 p-4 lg:p-6 overflow-auto">
          <Outlet />
        </main>
        <Footer />
      </div>
    </div>
  );
}
