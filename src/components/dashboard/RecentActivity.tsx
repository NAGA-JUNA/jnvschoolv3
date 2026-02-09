import { Card } from "@/components/ui/card";
import { ScrollArea } from "@/components/ui/scroll-area";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Skeleton } from "@/components/ui/skeleton";
import { cn } from "@/lib/utils";
import { formatDistanceToNow } from "date-fns";

interface ActivityItem {
  id: number;
  action: string;
  entity_type: string;
  entity_id: number;
  created_at: string;
  user_name: string;
  user_role: string;
}

const accentColors = [
  "border-l-kpi-blue",
  "border-l-kpi-green",
  "border-l-kpi-orange",
  "border-l-kpi-purple",
  "border-l-kpi-pink",
];

function getInitials(name: string) {
  return name
    .split(" ")
    .map((w) => w[0])
    .join("")
    .toUpperCase()
    .slice(0, 2);
}

function formatTime(dateStr: string) {
  try {
    return formatDistanceToNow(new Date(dateStr), { addSuffix: true });
  } catch {
    return dateStr;
  }
}

interface RecentActivityProps {
  items?: ActivityItem[];
  loading?: boolean;
}

export function RecentActivity({ items, loading }: RecentActivityProps) {
  return (
    <Card className="p-5">
      <div className="flex items-center justify-between mb-4">
        <h3 className="font-semibold">Recent Activity</h3>
        <span className="text-xs text-muted-foreground cursor-pointer hover:text-primary transition-colors">View All</span>
      </div>
      <ScrollArea className="h-[320px]">
        {loading ? (
          <div className="space-y-3">
            {Array.from({ length: 4 }).map((_, i) => (
              <Skeleton key={i} className="h-20 rounded-lg" />
            ))}
          </div>
        ) : !items || items.length === 0 ? (
          <p className="text-sm text-muted-foreground text-center py-8">No recent activity</p>
        ) : (
          <div className="space-y-3">
            {items.map((item, idx) => (
              <div
                key={item.id}
                className={cn(
                  "flex items-start gap-3 p-3 rounded-lg border-l-4 bg-muted/30 hover:bg-muted/50 transition-colors",
                  accentColors[idx % accentColors.length]
                )}
              >
                <Avatar className="h-9 w-9 flex-shrink-0">
                  <AvatarFallback className="bg-primary/10 text-primary text-xs font-semibold">
                    {getInitials(item.user_name)}
                  </AvatarFallback>
                </Avatar>
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2 mb-1">
                    <span className="text-sm font-semibold text-foreground">{item.user_name}</span>
                    <span className="text-[10px] px-1.5 py-0.5 rounded-full bg-muted text-muted-foreground font-medium">
                      {item.user_role}
                    </span>
                  </div>
                  <p className="text-xs text-muted-foreground leading-relaxed">
                    {item.action} — {item.entity_type}
                  </p>
                  <p className="text-[10px] text-muted-foreground/60 mt-1.5">{formatTime(item.created_at)}</p>
                </div>
              </div>
            ))}
          </div>
        )}
      </ScrollArea>
    </Card>
  );
}
