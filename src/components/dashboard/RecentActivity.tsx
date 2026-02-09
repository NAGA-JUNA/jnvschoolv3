import { Card } from "@/components/ui/card";
import { ScrollArea } from "@/components/ui/scroll-area";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { cn } from "@/lib/utils";

interface Announcement {
  id: number;
  author: string;
  initials: string;
  department: string;
  message: string;
  timestamp: string;
  accentColor: string;
}

const placeholderAnnouncements: Announcement[] = [
  {
    id: 1,
    author: "Principal Office",
    initials: "PO",
    department: "Administration",
    message: "School annual day preparations begin next week. All teachers to submit event proposals by Friday.",
    timestamp: "2 min ago",
    accentColor: "border-l-kpi-blue",
  },
  {
    id: 2,
    author: "Admissions Dept",
    initials: "AD",
    department: "Admissions",
    message: "New admission application received from Rahul Sharma for Class 5. Please review and process.",
    timestamp: "15 min ago",
    accentColor: "border-l-kpi-green",
  },
  {
    id: 3,
    author: "Gallery Admin",
    initials: "GA",
    department: "Media",
    message: "3 new photos uploaded to Annual Day album awaiting approval.",
    timestamp: "1 hour ago",
    accentColor: "border-l-kpi-orange",
  },
  {
    id: 4,
    author: "HR Department",
    initials: "HR",
    department: "Human Resources",
    message: "New teacher onboarded: Priya Singh — Mathematics Department.",
    timestamp: "2 hours ago",
    accentColor: "border-l-kpi-purple",
  },
  {
    id: 5,
    author: "Events Team",
    initials: "ET",
    department: "Events",
    message: "Parent-Teacher meeting scheduled for next Saturday at 10:00 AM.",
    timestamp: "3 hours ago",
    accentColor: "border-l-kpi-pink",
  },
];

interface RecentActivityProps {
  items?: Announcement[];
}

export function RecentActivity({ items = placeholderAnnouncements }: RecentActivityProps) {
  return (
    <Card className="p-5">
      <div className="flex items-center justify-between mb-4">
        <h3 className="font-semibold">Announcements</h3>
        <span className="text-xs text-muted-foreground cursor-pointer hover:text-primary transition-colors">View All</span>
      </div>
      <ScrollArea className="h-[320px]">
        <div className="space-y-3">
          {items.map((item) => (
            <div
              key={item.id}
              className={cn(
                "flex items-start gap-3 p-3 rounded-lg border-l-4 bg-muted/30 hover:bg-muted/50 transition-colors",
                item.accentColor
              )}
            >
              <Avatar className="h-9 w-9 flex-shrink-0">
                <AvatarFallback className="bg-primary/10 text-primary text-xs font-semibold">
                  {item.initials}
                </AvatarFallback>
              </Avatar>
              <div className="flex-1 min-w-0">
                <div className="flex items-center gap-2 mb-1">
                  <span className="text-sm font-semibold text-foreground">{item.author}</span>
                  <span className="text-[10px] px-1.5 py-0.5 rounded-full bg-muted text-muted-foreground font-medium">
                    {item.department}
                  </span>
                </div>
                <p className="text-xs text-muted-foreground leading-relaxed">{item.message}</p>
                <p className="text-[10px] text-muted-foreground/60 mt-1.5">{item.timestamp}</p>
              </div>
            </div>
          ))}
        </div>
      </ScrollArea>
    </Card>
  );
}
