import { Card } from "@/components/ui/card";
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from "recharts";

const placeholderData = [
  { month: "Jan", admissions: 12, notifications: 24, gallery: 8 },
  { month: "Feb", admissions: 19, notifications: 18, gallery: 12 },
  { month: "Mar", admissions: 8, notifications: 32, gallery: 15 },
  { month: "Apr", admissions: 22, notifications: 28, gallery: 10 },
  { month: "May", admissions: 15, notifications: 20, gallery: 18 },
  { month: "Jun", admissions: 30, notifications: 35, gallery: 22 },
];

interface TrendChartProps {
  data?: typeof placeholderData;
}

export function TrendChart({ data = placeholderData }: TrendChartProps) {
  return (
    <Card className="p-5">
      <div className="flex items-center justify-between mb-4">
        <h3 className="font-semibold">Session Recorded</h3>
        <span className="text-xs text-muted-foreground">Last 6 months</span>
      </div>
      <div className="h-[260px]">
        <ResponsiveContainer width="100%" height="100%">
          <BarChart data={data} barGap={2} barCategoryGap="20%">
            <CartesianGrid strokeDasharray="3 3" stroke="hsl(214, 32%, 91%)" vertical={false} />
            <XAxis dataKey="month" fontSize={12} tickLine={false} axisLine={false} />
            <YAxis fontSize={12} tickLine={false} axisLine={false} />
            <Tooltip
              contentStyle={{
                borderRadius: "8px",
                border: "1px solid hsl(214, 32%, 91%)",
                boxShadow: "0 4px 12px rgba(0,0,0,0.08)",
              }}
            />
            <Legend />
            <Bar dataKey="admissions" fill="hsl(221, 83%, 53%)" radius={[4, 4, 0, 0]} />
            <Bar dataKey="notifications" fill="hsl(142, 71%, 45%)" radius={[4, 4, 0, 0]} />
            <Bar dataKey="gallery" fill="hsl(25, 95%, 53%)" radius={[4, 4, 0, 0]} />
          </BarChart>
        </ResponsiveContainer>
      </div>
    </Card>
  );
}
