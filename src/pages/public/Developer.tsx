import { Globe, Code, Headphones, Mail, MessageCircle, ArrowLeft } from "lucide-react";
import { Link } from "react-router-dom";
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
    <div className="min-h-screen bg-background text-foreground">
      {/* Back link */}
      <div className="container mx-auto px-4 pt-6">
        <Link
          to="/"
          className="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground transition-colors"
        >
          <ArrowLeft className="h-4 w-4" />
          Back to Home
        </Link>
      </div>

      {/* Hero */}
      <section className="relative overflow-hidden py-20 lg:py-28">
        <div className="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-accent/10 pointer-events-none" />
        <div className="container mx-auto px-4 flex flex-col items-center text-center relative z-10">
          <img
            src={jnvLogo}
            alt="JNV Tech Logo"
            className="w-28 h-28 lg:w-36 lg:h-36 object-contain mb-6 drop-shadow-lg"
          />
          <h1 className="text-3xl lg:text-5xl font-extrabold tracking-tight mb-3">
            JNV Tech
          </h1>
          <p className="text-lg lg:text-xl text-primary font-semibold mb-4 italic">
            "Journey to New Value"
          </p>
          <p className="max-w-2xl text-muted-foreground text-base lg:text-lg leading-relaxed">
            We help businesses move from ideas to impact with modern web design,
            development, and reliable support. Our mission is to create real
            digital value that helps you grow.
          </p>
        </div>
      </section>

      {/* About */}
      <section className="py-16 bg-muted/40">
        <div className="container mx-auto px-4 max-w-3xl text-center">
          <h2 className="text-2xl lg:text-3xl font-bold mb-4">
            JNV Tech — Journey to New Value
          </h2>
          <p className="text-muted-foreground leading-relaxed text-base lg:text-lg">
            Founded with the belief that technology should empower, not
            complicate, JNV Tech partners with schools, startups, and small
            businesses to deliver purpose-built digital products. We combine
            clean design, robust engineering, and hands-on support so you can
            focus on what matters most — your mission.
          </p>
        </div>
      </section>

      {/* Services */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <h2 className="text-2xl lg:text-3xl font-bold text-center mb-10">
            What We Do
          </h2>
          <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            {services.map((s) => (
              <Card
                key={s.title}
                className="border border-border/60 shadow-sm hover:shadow-md transition-shadow"
              >
                <CardContent className="pt-6 flex flex-col items-center text-center gap-3">
                  <div className="bg-primary/10 rounded-xl p-3">
                    <s.icon className="h-7 w-7 text-primary" />
                  </div>
                  <h3 className="font-semibold text-lg">{s.title}</h3>
                  <p className="text-sm text-muted-foreground leading-relaxed">
                    {s.description}
                  </p>
                </CardContent>
              </Card>
            ))}
          </div>
        </div>
      </section>

      {/* Contact */}
      <section className="py-16 bg-muted/40">
        <div className="container mx-auto px-4 max-w-xl text-center">
          <h2 className="text-2xl lg:text-3xl font-bold mb-3">Get in Touch</h2>
          <p className="text-muted-foreground mb-8">
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
        </div>
      </section>

      {/* Page Footer */}
      <footer className="border-t py-8">
        <div className="container mx-auto px-4 flex flex-col items-center gap-2 text-center">
          <img src={jnvLogo} alt="JNV Tech" className="h-10 w-10 object-contain opacity-70" />
          <p className="text-sm text-muted-foreground">
            © {new Date().getFullYear()} JNV Tech. All rights reserved.
          </p>
          <p className="text-xs text-muted-foreground/60 italic">
            Journey to New Value
          </p>
        </div>
      </footer>
    </div>
  );
}
