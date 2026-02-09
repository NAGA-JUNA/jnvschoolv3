import { Card } from "@/components/ui/card";
import { Calendar } from "@/components/ui/calendar";
import { useState } from "react";

export function CalendarWidget() {
  const [date, setDate] = useState<Date | undefined>(new Date());

  return (
    <Card className="p-4">
      <h3 className="font-semibold mb-3 text-sm">Events Calendar</h3>
      <Calendar
        mode="single"
        selected={date}
        onSelect={setDate}
        className="rounded-md pointer-events-auto w-full"
      />
    </Card>
  );
}
