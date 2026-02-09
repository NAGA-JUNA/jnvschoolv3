import { GraduationCap } from "lucide-react";

export function Footer() {
  return (
    <footer className="border-t bg-card mt-auto py-4 px-6">
      <div className="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-muted-foreground">
        <div className="flex items-center gap-2">
          <GraduationCap className="h-4 w-4 text-primary" />
          <span className="font-semibold text-foreground">SchoolAdmin</span>
          <span>— Modern School Management</span>
        </div>
        <div className="flex items-center gap-3">
          <span>v1.0.0</span>
          <span>•</span>
          <span>© {new Date().getFullYear()} All rights reserved</span>
        </div>
      </div>
    </footer>
  );
}
