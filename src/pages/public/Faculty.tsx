import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { mockTeachers } from "@/data/mockTeachers";
import { GraduationCap, User, BookOpen, Calendar, Briefcase } from "lucide-react";

// Only show active teachers on the public page
const activeTeachers = mockTeachers.filter((t) => t.status === "active");

// Extract unique subjects for filter
const allSubjects = Array.from(new Set(activeTeachers.flatMap((t) => t.subjects))).sort();

export default function FacultyPage() {
  const [selectedSubject, setSelectedSubject] = useState<string>("All");

  const filteredTeachers =
    selectedSubject === "All"
      ? activeTeachers
      : activeTeachers.filter((t) => t.subjects.includes(selectedSubject));

  return (
    <div>
      {/* Hero Banner */}
      <section className="bg-gradient-to-br from-primary to-primary/80 text-primary-foreground py-16 px-4">
        <div className="max-w-4xl mx-auto text-center space-y-3">
          <h1 className="text-3xl md:text-4xl font-bold tracking-tight">Our Faculty</h1>
          <p className="text-lg opacity-90">
            Dedicated educators shaping the leaders of tomorrow
          </p>
        </div>
      </section>

      {/* Intro */}
      <section className="max-w-6xl mx-auto pt-10 px-4 text-center space-y-3">
        <p className="text-muted-foreground max-w-3xl mx-auto leading-relaxed">
          Our highly qualified and experienced faculty members are the backbone of JNV Model School. Each teacher brings passion, expertise, and a commitment to student success. Teacher profiles, photos, and details are managed through the Admin Panel and automatically reflected here.
        </p>
      </section>

      {/* Subject Filter */}
      <section className="max-w-6xl mx-auto pt-8 px-4">
        <div className="flex flex-wrap gap-2 justify-center">
          <Button
            variant={selectedSubject === "All" ? "default" : "outline"}
            size="sm"
            onClick={() => setSelectedSubject("All")}
          >
            All ({activeTeachers.length})
          </Button>
          {allSubjects.map((sub) => (
            <Button
              key={sub}
              variant={selectedSubject === sub ? "default" : "outline"}
              size="sm"
              onClick={() => setSelectedSubject(sub)}
            >
              {sub}
            </Button>
          ))}
        </div>
      </section>

      {/* Teacher Grid */}
      <section className="max-w-6xl mx-auto py-10 px-4">
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          {filteredTeachers.map((teacher) => (
            <Card key={teacher.id} className="overflow-hidden hover:shadow-lg transition-shadow group">
              {/* Photo */}
              <div className="aspect-[4/3] bg-gradient-to-br from-primary/10 to-primary/5 flex items-center justify-center">
                {teacher.photo_url ? (
                  <img
                    src={teacher.photo_url}
                    alt={teacher.full_name}
                    className="w-full h-full object-cover"
                  />
                ) : (
                  <User className="h-20 w-20 text-primary/25" />
                )}
              </div>

              {/* Details */}
              <div className="p-5 space-y-3">
                <div>
                  <h3 className="font-bold text-lg group-hover:text-primary transition-colors">
                    {teacher.full_name}
                  </h3>
                  <p className="text-sm text-muted-foreground">{teacher.qualification}</p>
                </div>

                <div className="flex flex-wrap gap-1.5">
                  {teacher.subjects.map((sub) => (
                    <Badge key={sub} variant="secondary" className="text-xs">
                      {sub}
                    </Badge>
                  ))}
                </div>

                <div className="space-y-1.5 text-xs text-muted-foreground">
                  <div className="flex items-center gap-2">
                    <Briefcase className="h-3.5 w-3.5" />
                    <span>{teacher.experience_years} years experience</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <BookOpen className="h-3.5 w-3.5" />
                    <span>{teacher.assigned_classes.join(", ")}</span>
                  </div>
                  <div className="flex items-center gap-2">
                    <Calendar className="h-3.5 w-3.5" />
                    <span>Joined {new Date(teacher.joining_date).getFullYear()}</span>
                  </div>
                </div>
              </div>
            </Card>
          ))}
        </div>

        {filteredTeachers.length === 0 && (
          <div className="text-center py-16 text-muted-foreground">
            <GraduationCap className="h-12 w-12 mx-auto mb-3 opacity-30" />
            <p>No teachers found for the selected subject.</p>
          </div>
        )}
      </section>
    </div>
  );
}
