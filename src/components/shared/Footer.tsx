import { GraduationCap } from "lucide-react";
import { Link } from "react-router-dom";
import { useTheme } from "@/contexts/ThemeContext";
import jnvLogo from "@/assets/jnvtech-logo.png";

export function Footer() {
  const { branding } = useTheme();

  return (
    <footer className="border-t bg-card mt-auto py-3 px-6">
      <div className="flex flex-col sm:flex-row items-center justify-between gap-2">
        <div className="flex items-center gap-2">
          {branding.logoUrl ? (
            <img src={branding.logoUrl} alt={branding.schoolName} className="h-6 w-6 object-contain" />
          ) : (
            <div className="bg-primary/10 rounded-lg p-1.5">
              <GraduationCap className="h-4 w-4 text-primary" />
            </div>
          )}
          <div>
            <span className="text-xs font-bold text-foreground">{branding.schoolName}</span>
            <span className="text-[10px] text-muted-foreground ml-1.5">— Modern School Management</span>
          </div>
        </div>
        <div className="flex items-center gap-3 text-[10px] text-muted-foreground">
          <span className="px-1.5 py-0.5 rounded bg-muted text-muted-foreground font-medium">v1.0.0</span>
          <span>
            © {new Date().getFullYear()} JSchoolAdmin. All rights reserved.
            {" "}
            <span className="mx-0.5">@</span>
            <Link to="/developer" className="inline-flex items-center gap-1 hover:text-foreground transition-colors font-medium">
              <img src={jnvLogo} alt="JNV Tech" className="h-3.5 w-3.5 object-contain inline-block" />
              JNV Tech
            </Link>
            {" "}- "Journey to New Value"
          </span>
        </div>
      </div>
    </footer>
  );
}
