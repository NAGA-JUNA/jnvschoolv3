import { Outlet, Link, useLocation, useNavigate } from "react-router-dom";
import { GraduationCap, Menu, X, Phone, Mail, MapPin, Clock, LogIn, ShieldCheck, User, Info, Eye, EyeOff } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
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

const DEMO_CREDENTIALS = [
  { role: "Super Admin", email: "admin@school.com", password: "admin123", nav: "/admin", icon: ShieldCheck },
  { role: "Office Staff", email: "office@school.com", password: "office123", nav: "/admin", icon: User },
  { role: "Teacher", email: "priya.singh@school.com", password: "teacher123", nav: "/teacher", icon: User },
] as const;

export function PublicLayout() {
  const location = useLocation();
  const navigate = useNavigate();
  const [menuOpen, setMenuOpen] = useState(false);
  const [loginOpen, setLoginOpen] = useState(false);
  const [role, setRole] = useState<"admin" | "teacher">("admin");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    await new Promise((r) => setTimeout(r, 800));
    setLoading(false);
    setLoginOpen(false);
    navigate(role === "admin" ? "/admin" : "/teacher");
  };

  const handleDemoFill = (cred: typeof DEMO_CREDENTIALS[number]) => {
    setEmail(cred.email);
    setPassword(cred.password);
    setRole(cred.nav === "/admin" ? "admin" : "teacher");
  };

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
            <Button size="sm" className="hidden lg:flex" onClick={() => setLoginOpen(true)}>
              Staff Login
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
            <Button size="sm" className="w-full mt-2" onClick={() => { setMenuOpen(false); setLoginOpen(true); }}>
              Staff Login
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

      {/* Staff Login Popup */}
      <Dialog open={loginOpen} onOpenChange={setLoginOpen}>
        <DialogContent className="sm:max-w-md">
          <DialogHeader>
            <div className="flex items-center gap-3">
              <div className="bg-primary rounded-lg p-2">
                <GraduationCap className="h-5 w-5 text-primary-foreground" />
              </div>
              <div>
                <DialogTitle>Staff Login</DialogTitle>
                <p className="text-sm text-muted-foreground">Sign in to access the staff portal</p>
              </div>
            </div>
          </DialogHeader>

          {/* Role tabs */}
          <div className="flex gap-2 p-1 bg-muted rounded-lg">
            <button
              type="button"
              className={`flex-1 py-2 rounded-md text-sm font-medium transition-all ${
                role === "admin" ? "bg-card text-foreground shadow-sm" : "text-muted-foreground"
              }`}
              onClick={() => setRole("admin")}
            >
              <ShieldCheck className="h-4 w-4 inline mr-1" />Admin
            </button>
            <button
              type="button"
              className={`flex-1 py-2 rounded-md text-sm font-medium transition-all ${
                role === "teacher" ? "bg-card text-foreground shadow-sm" : "text-muted-foreground"
              }`}
              onClick={() => setRole("teacher")}
            >
              <User className="h-4 w-4 inline mr-1" />Teacher
            </button>
          </div>

          <form onSubmit={handleLogin} className="space-y-4">
            <div className="space-y-1.5">
              <Label>Email</Label>
              <Input type="email" placeholder="you@school.com" required value={email} onChange={(e) => setEmail(e.target.value)} className="h-10" />
            </div>
            <div className="space-y-1.5">
              <Label>Password</Label>
              <div className="relative">
                <Input type={showPassword ? "text" : "password"} placeholder="••••••••" required value={password} onChange={(e) => setPassword(e.target.value)} className="h-10 pr-10" />
                <button type="button" className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground" onClick={() => setShowPassword(!showPassword)}>
                  {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </button>
              </div>
            </div>
            <Button type="submit" className="w-full h-10" disabled={loading}>
              {loading ? (
                <span className="flex items-center gap-2"><span className="h-4 w-4 border-2 border-primary-foreground/30 border-t-primary-foreground rounded-full animate-spin" /> Signing in...</span>
              ) : (
                <><LogIn className="h-4 w-4 mr-2" /> Sign In</>
              )}
            </Button>
          </form>

          {/* Demo quick access */}
          <div className="rounded-lg border bg-muted/50 p-3 space-y-2">
            <p className="text-xs font-medium text-muted-foreground flex items-center gap-1.5"><Info className="h-3.5 w-3.5" /> Quick Demo Access</p>
            {DEMO_CREDENTIALS.map((cred) => (
              <button
                key={cred.email}
                type="button"
                onClick={() => handleDemoFill(cred)}
                className="w-full flex items-center gap-2 rounded border bg-background px-3 py-2 text-left text-xs transition-colors hover:bg-accent/10 hover:border-primary/30"
              >
                <cred.icon className="h-3.5 w-3.5 text-primary" />
                <span className="font-medium">{cred.role}</span>
                <span className="text-muted-foreground ml-auto font-mono">{cred.password}</span>
              </button>
            ))}
          </div>
        </DialogContent>
      </Dialog>
    </div>
  );
}
