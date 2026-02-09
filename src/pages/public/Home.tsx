import { Bell, GraduationCap, Image, Calendar, ArrowRight, ChevronRight, Users, Award, BookOpen, Phone, Mail, MapPin, User, Star, CheckCircle } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Link } from "react-router-dom";
import { schoolInfo, schoolStats, principalInfo, mockPublicNotifications, mockPublicEvents, galleryCategories, facilities } from "@/data/mockSchoolData";
import { mockTeachers } from "@/data/mockTeachers";

const quickLinks = [
  { icon: Bell, title: "Notifications", description: "Latest school announcements", href: "/notifications", color: "bg-kpi-blue" },
  { icon: Image, title: "Gallery", description: "Browse school photos & videos", href: "/gallery", color: "bg-kpi-orange" },
  { icon: Calendar, title: "Events", description: "Upcoming school events", href: "/events", color: "bg-kpi-green" },
  { icon: GraduationCap, title: "Admissions", description: "Apply for admission online", href: "/admissions", color: "bg-kpi-purple" },
];

const featuredTeachers = mockTeachers.filter((t) => t.status === "active").slice(0, 4);
const latestNotices = mockPublicNotifications.slice(0, 3);
const upcomingEvents = mockPublicEvents.filter((e) => e.type !== "holiday").slice(0, 3);
const galleryPreview = galleryCategories.filter((c) => c.type === "images").flatMap((c) => c.items).slice(0, 6);

export default function PublicHome() {
  return (
    <div>
      {/* ===== HERO ===== */}
      <section className="bg-gradient-to-br from-primary via-primary/90 to-primary/80 text-primary-foreground py-20 md:py-28 px-4 relative overflow-hidden">
        <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0djJIMjR2LTJoMTJ6TTM2IDI0djJIMjR2LTJoMTJ6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-30" />
        <div className="max-w-5xl mx-auto text-center space-y-6 relative z-10">
          <Badge variant="secondary" className="text-sm px-4 py-1.5 bg-primary-foreground/15 text-primary-foreground border-primary-foreground/20">
            {schoolInfo.affiliation} Affiliated • Est. {schoolInfo.established}
          </Badge>
          <h1 className="text-4xl md:text-6xl font-bold tracking-tight leading-tight">
            Welcome to<br />{schoolInfo.name}
          </h1>
          <p className="text-lg md:text-xl opacity-90 max-w-3xl mx-auto">
            {schoolInfo.tagline}
          </p>
          <div className="flex gap-3 justify-center flex-wrap">
            <Button size="lg" variant="secondary" asChild className="text-base">
              <Link to="/admissions">Apply Now <ArrowRight className="ml-2 h-4 w-4" /></Link>
            </Button>
            <Button size="lg" variant="outline" className="bg-transparent border-primary-foreground/30 text-primary-foreground hover:bg-primary-foreground/10 text-base" asChild>
              <Link to="/about">Learn More</Link>
            </Button>
          </div>
        </div>
      </section>

      {/* ===== STATS ===== */}
      <section className="bg-card border-b">
        <div className="max-w-6xl mx-auto grid grid-cols-2 md:grid-cols-4">
          {schoolStats.map((stat, i) => (
            <div key={i} className="text-center py-8 px-4 border-r last:border-r-0 border-border">
              <p className="text-3xl md:text-4xl font-bold text-primary">
                {stat.value}{stat.suffix}
              </p>
              <p className="text-sm text-muted-foreground mt-1">{stat.label}</p>
            </div>
          ))}
        </div>
      </section>

      {/* ===== PRINCIPAL MESSAGE ===== */}
      <section className="max-w-6xl mx-auto py-16 px-4">
        <div className="grid md:grid-cols-3 gap-8 items-center">
          <div className="md:col-span-1 text-center">
            <div className="w-36 h-36 mx-auto rounded-full bg-gradient-to-br from-primary/15 to-primary/5 flex items-center justify-center mb-4">
              <GraduationCap className="h-16 w-16 text-primary/40" />
            </div>
            <h3 className="font-bold">{principalInfo.name}</h3>
            <p className="text-sm text-muted-foreground">{principalInfo.title}</p>
          </div>
          <div className="md:col-span-2 space-y-4">
            <h2 className="text-2xl font-bold">Message from the Principal</h2>
            <blockquote className="border-l-4 border-primary pl-5 text-muted-foreground leading-relaxed italic">
              "{principalInfo.message.substring(0, 300)}..."
            </blockquote>
            <Button variant="outline" asChild>
              <Link to="/about">Read More <ChevronRight className="h-4 w-4 ml-1" /></Link>
            </Button>
          </div>
        </div>
      </section>

      {/* ===== QUICK ACCESS ===== */}
      <section className="bg-muted/50 py-16 px-4">
        <div className="max-w-6xl mx-auto">
          <h2 className="text-2xl font-bold text-center mb-10">Quick Access</h2>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {quickLinks.map((f) => (
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
        </div>
      </section>

      {/* ===== FEATURED TEACHERS ===== */}
      <section className="max-w-6xl mx-auto py-16 px-4">
        <div className="flex items-center justify-between mb-10">
          <h2 className="text-2xl font-bold">Our Faculty</h2>
          <Button variant="outline" asChild>
            <Link to="/faculty">View All <ChevronRight className="h-4 w-4 ml-1" /></Link>
          </Button>
        </div>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-5">
          {featuredTeachers.map((teacher) => (
            <Card key={teacher.id} className="overflow-hidden hover:shadow-md transition-shadow text-center">
              <div className="aspect-square bg-gradient-to-br from-primary/10 to-primary/5 flex items-center justify-center">
                {teacher.photo_url ? (
                  <img src={teacher.photo_url} alt={teacher.full_name} className="w-full h-full object-cover" />
                ) : (
                  <User className="h-16 w-16 text-primary/20" />
                )}
              </div>
              <div className="p-4 space-y-1">
                <h3 className="font-semibold text-sm">{teacher.full_name}</h3>
                <p className="text-xs text-muted-foreground">{teacher.subjects.join(", ")}</p>
                <p className="text-xs text-muted-foreground">{teacher.qualification}</p>
              </div>
            </Card>
          ))}
        </div>
      </section>

      {/* ===== LATEST NOTIFICATIONS ===== */}
      <section className="bg-muted/50 py-16 px-4">
        <div className="max-w-4xl mx-auto">
          <div className="flex items-center justify-between mb-8">
            <h2 className="text-2xl font-bold">Latest Notifications</h2>
            <Button variant="outline" asChild>
              <Link to="/notifications">View All <ChevronRight className="h-4 w-4 ml-1" /></Link>
            </Button>
          </div>
          <div className="space-y-4">
            {latestNotices.map((n) => (
              <Card key={n.id} className="p-5 hover:shadow-md transition-shadow">
                <div className="flex items-start gap-4">
                  <div className="bg-primary/10 rounded-lg p-2 shrink-0">
                    <Bell className="h-5 w-5 text-primary" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2 mb-1 flex-wrap">
                      <h3 className="font-semibold">{n.title}</h3>
                      <Badge variant={n.urgency === "important" ? "default" : "secondary"} className="text-xs capitalize">{n.urgency}</Badge>
                    </div>
                    <p className="text-sm text-muted-foreground line-clamp-2">{n.body}</p>
                    <p className="text-xs text-muted-foreground mt-2">{n.created_at}</p>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* ===== UPCOMING EVENTS ===== */}
      <section className="max-w-4xl mx-auto py-16 px-4">
        <div className="flex items-center justify-between mb-8">
          <h2 className="text-2xl font-bold">Upcoming Events</h2>
          <Button variant="outline" asChild>
            <Link to="/events">View All <ChevronRight className="h-4 w-4 ml-1" /></Link>
          </Button>
        </div>
        <div className="space-y-4">
          {upcomingEvents.map((event) => (
            <Card key={event.id} className="p-5 hover:shadow-md transition-shadow">
              <div className="flex items-start gap-4">
                <div className="bg-primary/10 rounded-xl p-3 text-center min-w-[60px]">
                  <p className="text-xs text-primary font-medium">{new Date(event.date).toLocaleDateString("en", { month: "short" })}</p>
                  <p className="text-2xl font-bold text-primary">{new Date(event.date).getDate()}</p>
                </div>
                <div>
                  <h3 className="font-semibold text-lg">{event.title}</h3>
                  <p className="text-sm text-muted-foreground line-clamp-2 mt-1">{event.description}</p>
                  {event.location && <p className="text-xs text-muted-foreground mt-2 flex items-center gap-1"><MapPin className="h-3 w-3" />{event.location}</p>}
                </div>
              </div>
            </Card>
          ))}
        </div>
      </section>

      {/* ===== GALLERY PREVIEW ===== */}
      <section className="bg-muted/50 py-16 px-4">
        <div className="max-w-6xl mx-auto">
          <div className="flex items-center justify-between mb-8">
            <h2 className="text-2xl font-bold">Photo Gallery</h2>
            <Button variant="outline" asChild>
              <Link to="/gallery">View All <ChevronRight className="h-4 w-4 ml-1" /></Link>
            </Button>
          </div>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
            {galleryPreview.map((img) => (
              <div key={img.id} className="aspect-[4/3] rounded-xl overflow-hidden group">
                <img src={img.url} alt={img.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ===== ADMISSIONS CTA ===== */}
      <section className="bg-gradient-to-r from-primary to-primary/80 text-primary-foreground py-16 px-4">
        <div className="max-w-4xl mx-auto text-center space-y-5">
          <h2 className="text-3xl font-bold">Admissions Open for 2026-27</h2>
          <p className="text-lg opacity-90">Limited seats available for Nursery to Class 9. Apply today!</p>
          <Button size="lg" variant="secondary" asChild className="text-base">
            <Link to="/admissions">Apply Now <ArrowRight className="ml-2 h-4 w-4" /></Link>
          </Button>
        </div>
      </section>

      {/* ===== CONTACT STRIP ===== */}
      <section className="max-w-6xl mx-auto py-12 px-4">
        <div className="grid md:grid-cols-3 gap-6 text-center">
          <div className="flex flex-col items-center gap-2">
            <Phone className="h-6 w-6 text-primary" />
            <p className="font-semibold">Call Us</p>
            <p className="text-sm text-muted-foreground">{schoolInfo.phone}</p>
          </div>
          <div className="flex flex-col items-center gap-2">
            <Mail className="h-6 w-6 text-primary" />
            <p className="font-semibold">Email Us</p>
            <p className="text-sm text-muted-foreground">{schoolInfo.email}</p>
          </div>
          <div className="flex flex-col items-center gap-2">
            <MapPin className="h-6 w-6 text-primary" />
            <p className="font-semibold">Visit Us</p>
            <p className="text-sm text-muted-foreground">{schoolInfo.address}</p>
          </div>
        </div>
      </section>
    </div>
  );
}
