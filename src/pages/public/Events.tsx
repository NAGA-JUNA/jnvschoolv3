import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Calendar, MapPin, Clock } from "lucide-react";
import { cn } from "@/lib/utils";
import { useState } from "react";
import { mockPublicEvents } from "@/data/mockSchoolData";

const typeColors: Record<string, string> = {
  academic: "bg-kpi-blue/10 text-kpi-blue",
  cultural: "bg-kpi-purple/10 text-kpi-purple",
  sports: "bg-kpi-green/10 text-kpi-green",
  holiday: "bg-kpi-orange/10 text-kpi-orange",
  other: "bg-secondary text-secondary-foreground",
};

const eventTypes = ["all", "academic", "cultural", "sports", "holiday"] as const;

export default function PublicEvents() {
  const [filter, setFilter] = useState<string>("all");

  const filtered = filter === "all" ? mockPublicEvents : mockPublicEvents.filter((e) => e.type === filter);

  return (
    <div className="max-w-4xl mx-auto py-10 px-4 space-y-6">
      <div className="text-center space-y-2">
        <h1 className="text-3xl font-bold">School Events</h1>
        <p className="text-muted-foreground">Upcoming events and important dates</p>
      </div>

      {/* Type Filter */}
      <div className="flex flex-wrap gap-2 justify-center">
        {eventTypes.map((t) => (
          <Button key={t} variant={filter === t ? "default" : "outline"} size="sm" onClick={() => setFilter(t)} className="capitalize">
            {t}
          </Button>
        ))}
      </div>

      <div className="space-y-4">
        {filtered.map((event) => (
          <Card key={event.id} className="p-5 hover:shadow-md transition-shadow">
            <div className="flex items-start gap-4">
              <div className="bg-primary/10 rounded-xl p-3 text-center min-w-[60px]">
                <p className="text-xs text-primary font-medium">
                  {new Date(event.date).toLocaleDateString("en", { month: "short" })}
                </p>
                <p className="text-2xl font-bold text-primary">
                  {new Date(event.date).getDate()}
                </p>
              </div>
              <div className="flex-1">
                <div className="flex items-center gap-2 mb-1 flex-wrap">
                  <h3 className="font-semibold text-lg">{event.title}</h3>
                  <Badge variant="outline" className={cn("capitalize text-xs", typeColors[event.type])}>{event.type}</Badge>
                </div>
                <p className="text-sm text-muted-foreground mb-3">{event.description}</p>
                <div className="flex flex-wrap gap-4 text-xs text-muted-foreground">
                  <span className="flex items-center gap-1"><Calendar className="h-3.5 w-3.5" /> {event.date}</span>
                  {event.time && <span className="flex items-center gap-1"><Clock className="h-3.5 w-3.5" /> {event.time}</span>}
                  {event.location && <span className="flex items-center gap-1"><MapPin className="h-3.5 w-3.5" /> {event.location}</span>}
                </div>
              </div>
            </div>
          </Card>
        ))}
        {filtered.length === 0 && (
          <div className="text-center py-16 text-muted-foreground">
            <Calendar className="h-12 w-12 mx-auto mb-3 opacity-30" />
            <p>No events found for this category.</p>
          </div>
        )}
      </div>
    </div>
  );
}
