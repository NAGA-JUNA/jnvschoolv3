import { Card } from "@/components/ui/card";
import { AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Legend } from "recharts";

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
      <h3 className="font-semibold mb-4">Trends Overview</h3>
      <div className="h-[260px]">
        <ResponsiveContainer width="100%" height="100%">
          <AreaChart data={data}>
            <defs>
              <linearGradient id="admissions" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="hsl(221, 83%, 53%)" stopOpacity={0.2} />
                <stop offset="95%" stopColor="hsl(221, 83%, 53%)" stopOpacity={0} />
              </linearGradient>
              <linearGradient id="notifications" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="hsl(142, 71%, 45%)" stopOpacity={0.2} />
                <stop offset="95%" stopColor="hsl(142, 71%, 45%)" stopOpacity={0} />
              </linearGradient>
              <linearGradient id="gallery" x1="0" y1="0" x2="0" y2="1">
                <stop offset="5%" stopColor="hsl(25, 95%, 53%)" stopOpacity={0.2} />
                <stop offset="95%" stopColor="hsl(25, 95%, 53%)" stopOpacity={0} />
              </linearGradient>
            </defs>
            <CartesianGrid strokeDasharray="3 3" stroke="hsl(214, 32%, 91%)" />
            <XAxis dataKey="month" fontSize={12} tickLine={false} axisLine={false} />
            <YAxis fontSize={12} tickLine={false} axisLine={false} />
            <Tooltip />
            <Legend />
            <Area type="monotone" dataKey="admissions" stroke="hsl(221, 83%, 53%)" fill="url(#admissions)" strokeWidth={2} />
            <Area type="monotone" dataKey="notifications" stroke="hsl(142, 71%, 45%)" fill="url(#notifications)" strokeWidth={2} />
            <Area type="monotone" dataKey="gallery" stroke="hsl(25, 95%, 53%)" fill="url(#gallery)" strokeWidth={2} />
          </AreaChart>
        </ResponsiveContainer>
      </div>
    </Card>
  );
}
