import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Calendar, MapPin, Clock } from "lucide-react";
import { cn } from "@/lib/utils";

const typeColors: Record<string, string> = {
  academic: "bg-kpi-blue/10 text-kpi-blue",
  cultural: "bg-kpi-purple/10 text-kpi-purple",
  sports: "bg-kpi-green/10 text-kpi-green",
  holiday: "bg-kpi-orange/10 text-kpi-orange",
  other: "bg-secondary text-secondary-foreground",
};

const mockEvents = [
  { id: 1, title: "Parent-Teacher Meeting", description: "Quarterly PTM for all classes. Parents are requested to attend and discuss student progress with respective class teachers.", date: "2024-03-20", location: "School Auditorium", type: "academic" },
  { id: 2, title: "Annual Day Celebration", description: "Annual day celebration with cultural programs, award ceremony, and special performances by students.", date: "2024-04-10", end_date: "2024-04-11", location: "Main Ground", type: "cultural" },
  { id: 3, title: "Inter-House Sports Competition", description: "Annual sports day with track & field events, team sports, and athletics.", date: "2024-03-25", location: "Sports Complex", type: "sports" },
  { id: 4, title: "Holi Holiday", description: "School will remain closed on account of Holi festival.", date: "2024-03-25", type: "holiday" },
];

export default function PublicEvents() {
  return (
    <div className="max-w-4xl mx-auto py-10 px-4 space-y-6">
      <div className="text-center space-y-2">
        <h1 className="text-3xl font-bold">School Events</h1>
        <p className="text-muted-foreground">Upcoming events and important dates</p>
      </div>

      <div className="space-y-4">
        {mockEvents.map((event) => (
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
                <div className="flex items-center gap-2 mb-1">
                  <h3 className="font-semibold text-lg">{event.title}</h3>
                  <Badge variant="outline" className={cn("capitalize text-xs", typeColors[event.type])}>{event.type}</Badge>
                </div>
                <p className="text-sm text-muted-foreground mb-3">{event.description}</p>
                <div className="flex flex-wrap gap-4 text-xs text-muted-foreground">
                  <span className="flex items-center gap-1"><Calendar className="h-3.5 w-3.5" /> {event.date}{event.end_date ? ` — ${event.end_date}` : ""}</span>
                  {event.location && <span className="flex items-center gap-1"><MapPin className="h-3.5 w-3.5" /> {event.location}</span>}
                </div>
              </div>
            </div>
          </Card>
        ))}
      </div>
    </div>
  );
}
