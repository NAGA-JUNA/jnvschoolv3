import { Outlet } from "react-router-dom";
import { AppSidebar } from "@/components/layout/AppSidebar";
import { Footer } from "@/components/shared/Footer";

interface PanelLayoutProps {
  role: "admin" | "teacher";
}

export function PanelLayout({ role }: PanelLayoutProps) {
  return (
    <div className="flex min-h-screen w-full bg-background">
      <AppSidebar role={role} />
      <div className="flex-1 flex flex-col min-w-0">
        <main className="flex-1 p-4 lg:p-6 overflow-auto">
          <Outlet />
        </main>
        <Footer />
      </div>
    </div>
  );
}
