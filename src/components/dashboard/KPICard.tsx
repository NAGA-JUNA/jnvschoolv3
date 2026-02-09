import { LucideIcon } from "lucide-react";
import { Card } from "@/components/ui/card";
import { cn } from "@/lib/utils";

interface KPICardProps {
  title: string;
  value: number | string;
  icon: LucideIcon;
  color: "blue" | "green" | "orange" | "purple" | "pink";
  trend?: { value: number; label: string };
}

const iconBgMap = {
  blue: "bg-kpi-blue",
  green: "bg-kpi-green",
  orange: "bg-kpi-orange",
  purple: "bg-kpi-purple",
  pink: "bg-kpi-pink",
};

export function KPICard({ title, value, icon: Icon, color, trend }: KPICardProps) {
  return (
    <Card className="p-4 hover:shadow-md transition-shadow animate-fade-in">
      <div className="flex items-center gap-4">
        <div className={cn("h-12 w-12 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg", iconBgMap[color])}>
          <Icon className="h-5 w-5 text-white" />
        </div>
        <div className="min-w-0">
          <p className="text-xs font-medium text-muted-foreground uppercase tracking-wide">{title}</p>
          <p className="text-2xl font-bold tracking-tight text-foreground">{value}</p>
          {trend && (
            <p className={cn("text-[10px] font-medium", trend.value >= 0 ? "text-success" : "text-destructive")}>
              {trend.value >= 0 ? "↑" : "↓"} {Math.abs(trend.value)}% {trend.label}
            </p>
          )}
        </div>
      </div>
    </Card>
  );
}
