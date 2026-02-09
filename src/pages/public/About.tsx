import { Card } from "@/components/ui/card";
import { schoolInfo, principalInfo, visionMission, achievements, facilities } from "@/data/mockSchoolData";
import { GraduationCap, Eye, Target, Award, Star, Monitor, FlaskConical, Laptop, BookOpen, Trophy, Bus, HeartPulse, Theater } from "lucide-react";

const iconMap: Record<string, React.ElementType> = {
  Monitor, FlaskConical, Laptop, BookOpen, Trophy, Bus, HeartPulse, Theater,
};

export default function AboutPage() {
  return (
    <div>
      {/* Hero Banner */}
      <section className="bg-gradient-to-br from-primary to-primary/80 text-primary-foreground py-16 px-4">
        <div className="max-w-4xl mx-auto text-center space-y-3">
          <h1 className="text-3xl md:text-4xl font-bold tracking-tight">About Our School</h1>
          <p className="text-lg opacity-90">Nurturing minds since {schoolInfo.established}</p>
        </div>
      </section>

      {/* School Overview */}
      <section className="max-w-6xl mx-auto py-14 px-4 space-y-6">
        <div className="grid md:grid-cols-2 gap-10 items-center">
          <div className="space-y-4">
            <h2 className="text-2xl font-bold">Welcome to {schoolInfo.name}</h2>
            <p className="text-muted-foreground leading-relaxed">
              Established in {schoolInfo.established}, {schoolInfo.name} is a premier {schoolInfo.affiliation}-affiliated institution committed to providing quality education from Nursery to Class 12. Located in the heart of Lucknow, our school blends traditional values with modern teaching methodologies to prepare students for global challenges.
            </p>
            <p className="text-muted-foreground leading-relaxed">
              With over {new Date().getFullYear() - schoolInfo.established} years of academic excellence, we have nurtured thousands of students who have gone on to excel in diverse fields — from engineering and medicine to arts and civil services.
            </p>
            <div className="flex flex-wrap gap-3 pt-2">
              <span className="bg-primary/10 text-primary px-3 py-1.5 rounded-full text-sm font-medium">{schoolInfo.affiliation} Affiliated</span>
              <span className="bg-primary/10 text-primary px-3 py-1.5 rounded-full text-sm font-medium">Aff. No: {schoolInfo.affiliationNo}</span>
              <span className="bg-primary/10 text-primary px-3 py-1.5 rounded-full text-sm font-medium">{schoolInfo.medium} Medium</span>
            </div>
          </div>
          <div className="bg-gradient-to-br from-primary/5 to-primary/10 rounded-2xl p-8 flex items-center justify-center">
            <GraduationCap className="h-40 w-40 text-primary/30" />
          </div>
        </div>
      </section>

      {/* Vision & Mission */}
      <section className="bg-muted/50 py-14 px-4">
        <div className="max-w-6xl mx-auto space-y-10">
          <h2 className="text-2xl font-bold text-center">Our Vision & Mission</h2>
          <div className="grid md:grid-cols-2 gap-8">
            <Card className="p-6 space-y-4 border-l-4 border-l-primary">
              <div className="flex items-center gap-3">
                <div className="bg-primary/10 rounded-lg p-2.5">
                  <Eye className="h-6 w-6 text-primary" />
                </div>
                <h3 className="text-xl font-bold">Our Vision</h3>
              </div>
              <p className="text-muted-foreground leading-relaxed">{visionMission.vision}</p>
            </Card>
            <Card className="p-6 space-y-4 border-l-4 border-l-accent">
              <div className="flex items-center gap-3">
                <div className="bg-accent/10 rounded-lg p-2.5">
                  <Target className="h-6 w-6 text-accent" />
                </div>
                <h3 className="text-xl font-bold">Our Mission</h3>
              </div>
              <p className="text-muted-foreground leading-relaxed">{visionMission.mission}</p>
            </Card>
          </div>
        </div>
      </section>

      {/* Principal's Message */}
      <section className="max-w-6xl mx-auto py-14 px-4">
        <div className="grid md:grid-cols-3 gap-8 items-start">
          <div className="md:col-span-1">
            <Card className="p-6 text-center space-y-4">
              <div className="w-32 h-32 mx-auto rounded-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                <GraduationCap className="h-16 w-16 text-primary/50" />
              </div>
              <div>
                <h3 className="font-bold text-lg">{principalInfo.name}</h3>
                <p className="text-sm text-muted-foreground">{principalInfo.title}</p>
                <p className="text-xs text-muted-foreground mt-1">{principalInfo.qualification}</p>
              </div>
            </Card>
          </div>
          <div className="md:col-span-2 space-y-4">
            <h2 className="text-2xl font-bold">Principal's Message</h2>
            <blockquote className="border-l-4 border-primary pl-6 text-muted-foreground leading-relaxed italic text-lg">
              "{principalInfo.message}"
            </blockquote>
            <p className="text-right font-semibold text-primary">— {principalInfo.name}</p>
          </div>
        </div>
      </section>

      {/* Core Values */}
      <section className="bg-muted/50 py-14 px-4">
        <div className="max-w-6xl mx-auto space-y-10">
          <h2 className="text-2xl font-bold text-center">Our Core Values</h2>
          <div className="grid grid-cols-2 md:grid-cols-3 gap-5">
            {visionMission.values.map((v, i) => (
              <Card key={i} className="p-5 text-center space-y-2 hover:shadow-md transition-shadow">
                <Star className="h-8 w-8 text-accent mx-auto" />
                <h3 className="font-bold">{v.title}</h3>
                <p className="text-sm text-muted-foreground">{v.description}</p>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Facilities */}
      <section className="max-w-6xl mx-auto py-14 px-4 space-y-10">
        <h2 className="text-2xl font-bold text-center">Our Facilities</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {facilities.map((f, i) => {
            const Icon = iconMap[f.icon] || Monitor;
            return (
              <Card key={i} className="p-5 space-y-3 hover:shadow-md transition-shadow">
                <div className="bg-primary/10 w-12 h-12 rounded-xl flex items-center justify-center">
                  <Icon className="h-6 w-6 text-primary" />
                </div>
                <h3 className="font-semibold">{f.title}</h3>
                <p className="text-sm text-muted-foreground">{f.description}</p>
              </Card>
            );
          })}
        </div>
      </section>

      {/* Achievements */}
      <section className="bg-muted/50 py-14 px-4">
        <div className="max-w-4xl mx-auto space-y-10">
          <h2 className="text-2xl font-bold text-center">Achievements & Awards</h2>
          <div className="space-y-4">
            {achievements.map((a, i) => (
              <Card key={i} className="p-5 flex items-start gap-4 hover:shadow-md transition-shadow">
                <div className="bg-accent/10 rounded-xl p-3 text-center min-w-[60px]">
                  <Award className="h-5 w-5 text-accent mx-auto mb-1" />
                  <span className="text-xs font-bold text-accent">{a.year}</span>
                </div>
                <div>
                  <h3 className="font-semibold">{a.title}</h3>
                  <p className="text-sm text-muted-foreground mt-1">{a.description}</p>
                </div>
              </Card>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
