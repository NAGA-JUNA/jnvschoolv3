import { AlertTriangle, Info, XCircle, X } from "lucide-react";
import { useState } from "react";
import { cn } from "@/lib/utils";

interface AlertBannerProps {
  message: string;
  type: "info" | "warning" | "error";
  dismissible?: boolean;
}

const styles = {
  info: "bg-primary text-primary-foreground",
  warning: "bg-kpi-orange text-white",
  error: "bg-destructive text-destructive-foreground",
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
    <div className={cn("flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-sm", styles[type])}>
      <div className="p-1.5 rounded-full bg-white/20 flex-shrink-0">
        <Icon className="h-4 w-4" />
      </div>
      <p className="text-sm font-medium flex-1">{message}</p>
      {dismissible && (
        <button
          onClick={() => setVisible(false)}
          className="h-7 w-7 rounded-full bg-white/20 hover:bg-white/30 flex items-center justify-center transition-colors flex-shrink-0"
        >
          <X className="h-3.5 w-3.5" />
        </button>
      )}
    </div>
  );
}
