import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { academicInfo, facilities, schoolInfo } from "@/data/mockSchoolData";
import { BookOpen, Calendar, Trophy, FlaskConical, Laptop, Monitor, Bus, HeartPulse, Theater, GraduationCap } from "lucide-react";

const iconMap: Record<string, React.ElementType> = {
  Monitor, FlaskConical, Laptop, BookOpen, Trophy, Bus, HeartPulse, Theater,
};

export default function AcademicsPage() {
  return (
    <div>
      {/* Hero Banner */}
      <section className="bg-gradient-to-br from-primary to-primary/80 text-primary-foreground py-16 px-4">
        <div className="max-w-4xl mx-auto text-center space-y-3">
          <h1 className="text-3xl md:text-4xl font-bold tracking-tight">Academics</h1>
          <p className="text-lg opacity-90">{schoolInfo.affiliation}-affiliated curriculum from Nursery to Class 12</p>
        </div>
      </section>

      {/* Classes Offered */}
      <section className="max-w-6xl mx-auto py-14 px-4 space-y-8">
        <h2 className="text-2xl font-bold text-center">Classes Offered</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {academicInfo.classesOffered.map((group, i) => (
            <Card key={i} className="p-6 space-y-4 hover:shadow-md transition-shadow">
              <div className="flex items-center gap-3">
                <div className="bg-primary/10 rounded-lg p-2.5">
                  <GraduationCap className="h-5 w-5 text-primary" />
                </div>
                <h3 className="font-bold text-lg">{group.group}</h3>
              </div>
              <div className="flex flex-wrap gap-2">
                {group.classes.map((c) => (
                  <Badge key={c} variant="secondary">{c}</Badge>
                ))}
              </div>
              <p className="text-sm text-muted-foreground">
                Curriculum: <span className="font-medium text-foreground">{group.curriculum}</span>
              </p>
              {group.streams && (
                <div className="pt-2 border-t">
                  <p className="text-xs font-medium text-muted-foreground mb-2">Streams Available:</p>
                  <div className="flex flex-wrap gap-1.5">
                    {group.streams.map((s) => (
                      <Badge key={s} variant="outline" className="text-xs">{s}</Badge>
                    ))}
                  </div>
                </div>
              )}
            </Card>
          ))}
        </div>
      </section>

      {/* Subjects by Level */}
      <section className="bg-muted/50 py-14 px-4">
        <div className="max-w-6xl mx-auto space-y-8">
          <h2 className="text-2xl font-bold text-center">Subject Curriculum</h2>
          <Tabs defaultValue="primary" className="w-full">
            <TabsList className="flex flex-wrap h-auto gap-1 justify-center mb-6">
              <TabsTrigger value="primary">Primary (1–5)</TabsTrigger>
              <TabsTrigger value="middle">Middle (6–8)</TabsTrigger>
              <TabsTrigger value="secondary">Secondary (9–10)</TabsTrigger>
              <TabsTrigger value="senior">Senior Secondary</TabsTrigger>
            </TabsList>

            <TabsContent value="primary">
              <Card className="p-6">
                <div className="flex flex-wrap gap-2">
                  {academicInfo.subjects.primary.map((s) => (
                    <Badge key={s} className="text-sm py-1.5 px-3">{s}</Badge>
                  ))}
                </div>
              </Card>
            </TabsContent>
            <TabsContent value="middle">
              <Card className="p-6">
                <div className="flex flex-wrap gap-2">
                  {academicInfo.subjects.middle.map((s) => (
                    <Badge key={s} className="text-sm py-1.5 px-3">{s}</Badge>
                  ))}
                </div>
              </Card>
            </TabsContent>
            <TabsContent value="secondary">
              <Card className="p-6">
                <div className="flex flex-wrap gap-2">
                  {academicInfo.subjects.secondary.map((s) => (
                    <Badge key={s} className="text-sm py-1.5 px-3">{s}</Badge>
                  ))}
                </div>
              </Card>
            </TabsContent>
            <TabsContent value="senior">
              <div className="grid md:grid-cols-3 gap-4">
                <Card className="p-5 space-y-3">
                  <h4 className="font-bold text-sm">🔬 Science Stream</h4>
                  <div className="flex flex-wrap gap-1.5">
                    {academicInfo.subjects.seniorScience.map((s) => (
                      <Badge key={s} variant="secondary" className="text-xs">{s}</Badge>
                    ))}
                  </div>
                </Card>
                <Card className="p-5 space-y-3">
                  <h4 className="font-bold text-sm">📊 Commerce Stream</h4>
                  <div className="flex flex-wrap gap-1.5">
                    {academicInfo.subjects.seniorCommerce.map((s) => (
                      <Badge key={s} variant="secondary" className="text-xs">{s}</Badge>
                    ))}
                  </div>
                </Card>
                <Card className="p-5 space-y-3">
                  <h4 className="font-bold text-sm">📚 Humanities Stream</h4>
                  <div className="flex flex-wrap gap-1.5">
                    {academicInfo.subjects.seniorHumanities.map((s) => (
                      <Badge key={s} variant="secondary" className="text-xs">{s}</Badge>
                    ))}
                  </div>
                </Card>
              </div>
            </TabsContent>
          </Tabs>
        </div>
      </section>

      {/* Exam Pattern */}
      <section className="max-w-4xl mx-auto py-14 px-4 space-y-8">
        <h2 className="text-2xl font-bold text-center">Examination Pattern</h2>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          {academicInfo.examPattern.map((exam, i) => (
            <Card key={i} className="p-5 text-center space-y-2 hover:shadow-md transition-shadow">
              <Calendar className="h-8 w-8 text-primary mx-auto" />
              <h3 className="font-bold text-sm">{exam.name}</h3>
              <p className="text-xs text-muted-foreground">{exam.month}</p>
              <Badge variant="outline" className="text-xs">{exam.weightage}</Badge>
            </Card>
          ))}
        </div>
      </section>

      {/* Extra-Curricular */}
      <section className="bg-muted/50 py-14 px-4">
        <div className="max-w-6xl mx-auto space-y-8">
          <h2 className="text-2xl font-bold text-center">Extra-Curricular Activities</h2>
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            {academicInfo.activities.map((activity, i) => (
              <Card key={i} className="p-4 text-center hover:shadow-md transition-shadow">
                <p className="text-sm font-medium">{activity}</p>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Facilities */}
      <section className="max-w-6xl mx-auto py-14 px-4 space-y-8">
        <h2 className="text-2xl font-bold text-center">Infrastructure & Facilities</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          {facilities.map((f, i) => {
            const Icon = iconMap[f.icon] || BookOpen;
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
    </div>
  );
}
