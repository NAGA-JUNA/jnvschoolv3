import { Card } from "@/components/ui/card";
import { Calendar } from "@/components/ui/calendar";
import { useState } from "react";

export function CalendarWidget() {
  const [date, setDate] = useState<Date | undefined>(new Date());

  return (
    <Card className="p-4">
      <div className="flex items-center justify-between mb-3">
        <h3 className="font-semibold text-sm">Calendar & Activities</h3>
        <span className="text-[10px] px-2 py-0.5 rounded-full bg-primary/10 text-primary font-medium">Today</span>
      </div>
      <Calendar
        mode="single"
        selected={date}
        onSelect={setDate}
        className="rounded-md pointer-events-auto w-full"
      />
    </Card>
  );
}
