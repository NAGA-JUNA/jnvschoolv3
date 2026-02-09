import { Bell, GraduationCap, Image, Calendar, ArrowRight, ChevronRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Link } from "react-router-dom";

const features = [
  { icon: Bell, title: "Notifications", description: "Stay updated with school announcements", href: "/notifications", color: "bg-kpi-blue" },
  { icon: Image, title: "Gallery", description: "Browse school photos and videos", href: "/gallery", color: "bg-kpi-orange" },
  { icon: Calendar, title: "Events", description: "View upcoming school events", href: "/events", color: "bg-kpi-green" },
  { icon: GraduationCap, title: "Admissions", description: "Apply for admission online", href: "/admissions", color: "bg-kpi-purple" },
];

export default function PublicHome() {
  return (
    <div>
      {/* Hero */}
      <section className="bg-gradient-to-br from-primary to-primary/80 text-primary-foreground py-20 px-4">
        <div className="max-w-4xl mx-auto text-center space-y-6">
          <h1 className="text-4xl md:text-5xl font-bold tracking-tight">Welcome to Our School</h1>
          <p className="text-lg md:text-xl opacity-90 max-w-2xl mx-auto">
            Empowering young minds with quality education, modern facilities, and a nurturing environment.
          </p>
          <div className="flex gap-3 justify-center">
            <Button size="lg" variant="secondary" asChild>
              <Link to="/admissions">Apply Now <ArrowRight className="ml-2 h-4 w-4" /></Link>
            </Button>
            <Button size="lg" variant="outline" className="bg-transparent border-primary-foreground/30 text-primary-foreground hover:bg-primary-foreground/10" asChild>
              <Link to="/notifications">View Notices</Link>
            </Button>
          </div>
        </div>
      </section>

      {/* Features */}
      <section className="max-w-6xl mx-auto py-16 px-4">
        <h2 className="text-2xl font-bold text-center mb-10">Quick Access</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {features.map((f) => (
            <Link key={f.href} to={f.href}>
              <Card className="p-6 hover:shadow-lg transition-shadow group h-full">
                <div className={`${f.color} w-12 h-12 rounded-xl flex items-center justify-center mb-4`}>
                  <f.icon className="h-6 w-6 text-white" />
                </div>
                <h3 className="font-semibold text-lg mb-2 group-hover:text-primary transition-colors">{f.title}</h3>
                <p className="text-sm text-muted-foreground">{f.description}</p>
                <div className="flex items-center text-primary text-sm font-medium mt-3 gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  View <ChevronRight className="h-4 w-4" />
                </div>
              </Card>
            </Link>
          ))}
        </div>
      </section>
    </div>
  );
}
