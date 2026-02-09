import { Globe, Code, Headphones, Mail, MessageCircle } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import jnvLogo from "@/assets/jnvtech-logo.png";

const services = [
  {
    icon: Globe,
    title: "Web Design & Development",
    description:
      "Custom, responsive websites and web applications built with modern technologies to bring your vision to life.",
  },
  {
    icon: Code,
    title: "School Management Systems",
    description:
      "End-to-end digital solutions for educational institutions — admissions, attendance, communication, and more.",
  },
  {
    icon: Headphones,
    title: "Digital Solutions & Support",
    description:
      "Ongoing maintenance, hosting support, and digital strategy consulting to keep your business running smoothly.",
  },
];

export default function DeveloperPage() {
  return (
    <div className="space-y-8 max-w-5xl mx-auto">
      {/* Hero */}
      <section className="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary/10 via-transparent to-accent/10 py-14 lg:py-20 px-6">
        <div className="flex flex-col items-center text-center relative z-10">
          <img
            src={jnvLogo}
            alt="JNV Tech Logo"
            className="w-24 h-24 lg:w-32 lg:h-32 object-contain mb-5 drop-shadow-lg"
          />
          <h1 className="text-2xl lg:text-4xl font-extrabold tracking-tight mb-2 text-foreground">
            JNV Tech
          </h1>
          <p className="text-base lg:text-lg text-primary font-semibold mb-3 italic">
            "Journey to New Value"
          </p>
          <p className="max-w-2xl text-muted-foreground text-sm lg:text-base leading-relaxed">
            We help businesses move from ideas to impact with modern web design,
            development, and reliable support. Our mission is to create real
            digital value that helps you grow.
          </p>
        </div>
      </section>

      {/* About */}
      <section className="rounded-xl bg-muted/40 p-8 text-center">
        <h2 className="text-xl lg:text-2xl font-bold mb-3 text-foreground">
          JNV Tech — Journey to New Value
        </h2>
        <p className="text-muted-foreground leading-relaxed text-sm lg:text-base max-w-3xl mx-auto">
          Founded with the belief that technology should empower, not
          complicate, JNV Tech partners with schools, startups, and small
          businesses to deliver purpose-built digital products. We combine
          clean design, robust engineering, and hands-on support so you can
          focus on what matters most — your mission.
        </p>
      </section>

      {/* Services */}
      <section>
        <h2 className="text-xl lg:text-2xl font-bold text-center mb-6 text-foreground">
          What We Do
        </h2>
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
          {services.map((s) => (
            <Card
              key={s.title}
              className="border border-border/60 shadow-sm hover:shadow-md transition-shadow"
            >
              <CardContent className="pt-6 flex flex-col items-center text-center gap-3">
                <div className="bg-primary/10 rounded-xl p-3">
                  <s.icon className="h-6 w-6 text-primary" />
                </div>
                <h3 className="font-semibold text-base">{s.title}</h3>
                <p className="text-sm text-muted-foreground leading-relaxed">
                  {s.description}
                </p>
              </CardContent>
            </Card>
          ))}
        </div>
      </section>

      {/* Contact */}
      <section className="rounded-xl bg-muted/40 p-8 text-center">
        <h2 className="text-xl lg:text-2xl font-bold mb-2 text-foreground">Get in Touch</h2>
        <p className="text-muted-foreground mb-6 text-sm">
          Reach out to us for custom solutions and support.
        </p>

        <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
          <Button
            asChild
            size="lg"
            className="bg-[hsl(142,70%,42%)] hover:bg-[hsl(142,70%,36%)] text-white gap-2 w-full sm:w-auto"
          >
            <a
              href="https://wa.me/918106811171"
              target="_blank"
              rel="noopener noreferrer"
            >
              <MessageCircle className="h-5 w-5" />
              WhatsApp Us
            </a>
          </Button>

          <Button
            asChild
            size="lg"
            variant="outline"
            className="border-primary text-primary hover:bg-primary/10 gap-2 w-full sm:w-auto"
          >
            <a href="mailto:contact@jnvtech.com">
              <Mail className="h-5 w-5" />
              Email Us
            </a>
          </Button>
        </div>
      </section>

      {/* Page Footer */}
      <div className="flex flex-col items-center gap-1.5 text-center py-4">
        <img src={jnvLogo} alt="JNV Tech" className="h-8 w-8 object-contain opacity-70" />
        <p className="text-xs text-muted-foreground">
          © {new Date().getFullYear()} JNV Tech. All rights reserved.
        </p>
        <p className="text-[10px] text-muted-foreground/60 italic">
          Journey to New Value
        </p>
      </div>
    </div>
  );
}
