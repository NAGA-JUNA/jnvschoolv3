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

const colorMap = {
  blue: "bg-kpi-blue/10 text-kpi-blue",
  green: "bg-kpi-green/10 text-kpi-green",
  orange: "bg-kpi-orange/10 text-kpi-orange",
  purple: "bg-kpi-purple/10 text-kpi-purple",
  pink: "bg-kpi-pink/10 text-kpi-pink",
};

const iconBgMap = {
  blue: "bg-kpi-blue",
  green: "bg-kpi-green",
  orange: "bg-kpi-orange",
  purple: "bg-kpi-purple",
  pink: "bg-kpi-pink",
};

export function KPICard({ title, value, icon: Icon, color, trend }: KPICardProps) {
  return (
    <Card className="p-5 hover:shadow-md transition-shadow animate-fade-in">
      <div className="flex items-start justify-between">
        <div className="space-y-2">
          <p className="text-sm font-medium text-muted-foreground">{title}</p>
          <p className="text-3xl font-bold tracking-tight">{value}</p>
          {trend && (
            <p className={cn("text-xs font-medium", trend.value >= 0 ? "text-success" : "text-destructive")}>
              {trend.value >= 0 ? "↑" : "↓"} {Math.abs(trend.value)}% {trend.label}
            </p>
          )}
        </div>
        <div className={cn("p-3 rounded-xl", iconBgMap[color])}>
          <Icon className="h-5 w-5 text-white" />
        </div>
      </div>
    </Card>
  );
}
