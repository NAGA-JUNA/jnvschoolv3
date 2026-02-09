import { Outlet, Link, useLocation } from "react-router-dom";
import { GraduationCap, Menu, X, Phone, Mail, MapPin, Clock } from "lucide-react";
import { Button } from "@/components/ui/button";
import { cn } from "@/lib/utils";
import { useState } from "react";
import { schoolInfo } from "@/data/mockSchoolData";

const publicLinks = [
  { label: "Home", href: "/" },
  { label: "About", href: "/about" },
  { label: "Academics", href: "/academics" },
  { label: "Faculty", href: "/faculty" },
  { label: "Gallery", href: "/gallery" },
  { label: "Events", href: "/events" },
  { label: "Notifications", href: "/notifications" },
  { label: "Admissions", href: "/admissions" },
  { label: "Contact", href: "/contact" },
];

const quickLinks = [
  { label: "Home", href: "/" },
  { label: "About Us", href: "/about" },
  { label: "Academics", href: "/academics" },
  { label: "Faculty", href: "/faculty" },
  { label: "Gallery", href: "/gallery" },
  { label: "Events", href: "/events" },
  { label: "Notifications", href: "/notifications" },
  { label: "Admissions", href: "/admissions" },
  { label: "Contact Us", href: "/contact" },
  { label: "Staff Login", href: "/login" },
];

export function PublicLayout() {
  const location = useLocation();
  const [menuOpen, setMenuOpen] = useState(false);

  return (
    <div className="min-h-screen flex flex-col bg-background">
      {/* Top Info Bar */}
      <div className="bg-primary text-primary-foreground text-xs py-1.5 px-4 hidden md:block">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center gap-4">
            <span className="flex items-center gap-1"><Phone className="h-3 w-3" /> {schoolInfo.phone}</span>
            <span className="flex items-center gap-1"><Mail className="h-3 w-3" /> {schoolInfo.email}</span>
          </div>
          <span className="flex items-center gap-1"><MapPin className="h-3 w-3" /> {schoolInfo.address}</span>
        </div>
      </div>

      {/* Header */}
      <header className="sticky top-0 z-30 bg-card border-b shadow-sm">
        <div className="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
          <Link to="/" className="flex items-center gap-2.5">
            <div className="bg-primary rounded-lg p-2">
              <GraduationCap className="h-5 w-5 text-primary-foreground" />
            </div>
            <div className="hidden sm:block">
              <span className="font-bold text-lg leading-tight block">{schoolInfo.name}</span>
              <span className="text-xs text-muted-foreground leading-tight">{schoolInfo.affiliation} Affiliated</span>
            </div>
            <span className="font-bold text-lg sm:hidden">{schoolInfo.shortName}</span>
          </Link>

          {/* Desktop nav */}
          <nav className="hidden lg:flex items-center gap-0.5">
            {publicLinks.map((link) => (
              <Link
                key={link.href}
                to={link.href}
                className={cn(
                  "px-3 py-2 rounded-lg text-sm font-medium transition-colors",
                  location.pathname === link.href
                    ? "bg-primary text-primary-foreground"
                    : "text-muted-foreground hover:text-foreground hover:bg-muted"
                )}
              >
                {link.label}
              </Link>
            ))}
          </nav>

          <div className="flex items-center gap-2">
            <Button asChild size="sm" className="hidden lg:flex">
              <Link to="/login">Staff Login</Link>
            </Button>
            <Button
              variant="ghost"
              size="icon"
              className="lg:hidden"
              onClick={() => setMenuOpen(!menuOpen)}
            >
              {menuOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
            </Button>
          </div>
        </div>

        {/* Mobile nav */}
        {menuOpen && (
          <div className="lg:hidden border-t bg-card px-4 py-3 space-y-1 max-h-[70vh] overflow-y-auto">
            {publicLinks.map((link) => (
              <Link
                key={link.href}
                to={link.href}
                onClick={() => setMenuOpen(false)}
                className={cn(
                  "block px-4 py-2.5 rounded-lg text-sm font-medium",
                  location.pathname === link.href
                    ? "bg-primary text-primary-foreground"
                    : "text-muted-foreground hover:bg-muted"
                )}
              >
                {link.label}
              </Link>
            ))}
            <Button asChild size="sm" className="w-full mt-2">
              <Link to="/login" onClick={() => setMenuOpen(false)}>Staff Login</Link>
            </Button>
          </div>
        )}
      </header>

      <main className="flex-1">
        <Outlet />
      </main>

      {/* Rich Footer */}
      <footer className="border-t bg-card">
        <div className="max-w-7xl mx-auto px-4 py-12">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {/* Column 1: About */}
            <div className="space-y-4">
              <div className="flex items-center gap-2">
                <div className="bg-primary rounded-lg p-2">
                  <GraduationCap className="h-5 w-5 text-primary-foreground" />
                </div>
                <div>
                  <span className="font-bold text-lg block">{schoolInfo.name}</span>
                  <span className="text-xs text-muted-foreground">{schoolInfo.tagline}</span>
                </div>
              </div>
              <p className="text-sm text-muted-foreground leading-relaxed">
                A premier {schoolInfo.affiliation}-affiliated school in Lucknow providing quality education from Nursery to Class 12 since {schoolInfo.established}.
              </p>
            </div>

            {/* Column 2: Quick Links */}
            <div>
              <h3 className="font-bold mb-4">Quick Links</h3>
              <ul className="space-y-2">
                {quickLinks.map((link) => (
                  <li key={link.href}>
                    <Link to={link.href} className="text-sm text-muted-foreground hover:text-primary transition-colors">
                      {link.label}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>

            {/* Column 3: Contact Info */}
            <div>
              <h3 className="font-bold mb-4">Contact Us</h3>
              <div className="space-y-3 text-sm text-muted-foreground">
                <div className="flex items-start gap-2">
                  <MapPin className="h-4 w-4 shrink-0 mt-0.5 text-primary" />
                  <span>{schoolInfo.address}</span>
                </div>
                <div className="flex items-center gap-2">
                  <Phone className="h-4 w-4 text-primary" />
                  <span>{schoolInfo.phone}</span>
                </div>
                <div className="flex items-center gap-2">
                  <Mail className="h-4 w-4 text-primary" />
                  <span>{schoolInfo.email}</span>
                </div>
              </div>
            </div>

            {/* Column 4: Office Hours */}
            <div>
              <h3 className="font-bold mb-4">Office Hours</h3>
              <div className="space-y-2 text-sm text-muted-foreground">
                <div className="flex items-center gap-2">
                  <Clock className="h-4 w-4 text-primary" />
                  <span>Mon – Fri</span>
                </div>
                <p className="pl-6">{schoolInfo.officeHours.weekdays}</p>
                <div className="flex items-center gap-2">
                  <Clock className="h-4 w-4 text-primary" />
                  <span>Saturday</span>
                </div>
                <p className="pl-6">{schoolInfo.officeHours.saturday}</p>
                <p className="text-xs mt-3">Sunday: {schoolInfo.officeHours.sunday}</p>
              </div>
            </div>
          </div>
        </div>

        {/* Copyright Bar */}
        <div className="border-t py-4 px-4">
          <div className="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-muted-foreground">
            <p>© {new Date().getFullYear()} {schoolInfo.name}. All rights reserved.</p>
            <p>Powered by <span className="font-semibold text-foreground">JNV Tech</span> — JSchoolAdmin v1.1.0</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
