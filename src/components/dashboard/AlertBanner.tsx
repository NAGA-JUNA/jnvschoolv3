import { AlertTriangle, Info, XCircle, X } from "lucide-react";
import { useState } from "react";
import { cn } from "@/lib/utils";

interface AlertBannerProps {
  message: string;
  type: "info" | "warning" | "error";
  dismissible?: boolean;
}

const styles = {
  info: "bg-info/10 border-info/30 text-info",
  warning: "bg-warning/10 border-warning/30 text-warning",
  error: "bg-destructive/10 border-destructive/30 text-destructive",
};

const icons = {
  info: Info,
  warning: AlertTriangle,
  error: XCircle,
};

export function AlertBanner({ message, type, dismissible = true }: AlertBannerProps) {
  const [visible, setVisible] = useState(true);
  if (!visible) return null;

  const Icon = icons[type];

  return (
    <div className={cn("flex items-center gap-3 p-4 rounded-lg border", styles[type])}>
      <Icon className="h-5 w-5 flex-shrink-0" />
      <p className="text-sm font-medium flex-1">{message}</p>
      {dismissible && (
        <button onClick={() => setVisible(false)} className="hover:opacity-70">
          <X className="h-4 w-4" />
        </button>
      )}
    </div>
  );
}
