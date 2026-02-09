import { GraduationCap } from "lucide-react";

export function Footer() {
  return (
    <footer className="border-t bg-card mt-auto py-3 px-6">
      <div className="flex flex-col sm:flex-row items-center justify-between gap-2">
        <div className="flex items-center gap-2">
          <div className="bg-primary/10 rounded-lg p-1.5">
            <GraduationCap className="h-4 w-4 text-primary" />
          </div>
          <div>
            <span className="text-xs font-bold text-foreground">SchoolAdmin</span>
            <span className="text-[10px] text-muted-foreground ml-1.5">— Modern School Management</span>
          </div>
        </div>
        <div className="flex items-center gap-3 text-[10px] text-muted-foreground">
          <span className="px-1.5 py-0.5 rounded bg-muted text-muted-foreground font-medium">v1.0.0</span>
          <span>© {new Date().getFullYear()} SchoolAdmin. All rights reserved.</span>
        </div>
      </div>
    </footer>
  );
}
