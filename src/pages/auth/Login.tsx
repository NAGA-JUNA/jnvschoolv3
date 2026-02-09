import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { GraduationCap, LogIn, Info, User, ShieldCheck, Eye, EyeOff, ArrowLeft } from "lucide-react";
import { Link, useNavigate } from "react-router-dom";
import { schoolInfo } from "@/data/mockSchoolData";
import jnvLogo from "@/assets/jnvtech-logo.png";
import api from "@/api/client";
import { AUTH } from "@/api/endpoints";
import { toast } from "sonner";

const DEMO_CREDENTIALS = [
  { role: "Super Admin", email: "admin@school.com", password: "admin123", nav: "/admin", icon: ShieldCheck },
  { role: "Office Staff", email: "office@school.com", password: "office123", nav: "/admin", icon: User },
  { role: "Teacher", email: "priya.singh@school.com", password: "teacher123", nav: "/teacher", icon: User },
] as const;

export default function LoginPage() {
  const navigate = useNavigate();
  const [role, setRole] = useState<"admin" | "teacher">("admin");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await api.post<{ token: string; user: { role: string } }>(AUTH.login, { email, password });
      const { token, user } = res.data;
      api.setToken(token);
      navigate(user?.role === "teacher" ? "/teacher" : "/admin");
    } catch (err: any) {
      toast.error(err.message || "Login failed. Check your credentials.");
    } finally {
      setLoading(false);
    }
  };

  const handleDemoFill = (cred: typeof DEMO_CREDENTIALS[number]) => {
    setEmail(cred.email);
    setPassword(cred.password);
    setRole(cred.nav === "/admin" ? "admin" : "teacher");
  };

  return (
    <div className="min-h-screen flex">
      {/* Left panel - branding */}
      <div className="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary via-primary/90 to-primary/70 relative overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-20 -left-10 w-72 h-72 rounded-full bg-primary-foreground/20 blur-3xl" />
          <div className="absolute bottom-20 right-10 w-96 h-96 rounded-full bg-primary-foreground/10 blur-3xl" />
        </div>
        <div className="relative z-10 flex flex-col items-center justify-center w-full px-12 text-primary-foreground">
          <div className="bg-primary-foreground/15 backdrop-blur-sm rounded-2xl p-5 mb-8">
            <GraduationCap className="h-16 w-16" />
          </div>
          <h1 className="text-4xl font-bold text-center mb-3">{schoolInfo.name}</h1>
          <p className="text-lg opacity-90 mb-2">{schoolInfo.affiliation} Affiliated</p>
          <p className="text-sm opacity-70 text-center max-w-sm">{schoolInfo.tagline}</p>
          <div className="mt-12 grid grid-cols-3 gap-8 text-center">
            <div>
              <p className="text-3xl font-bold">1500+</p>
              <p className="text-xs opacity-70 mt-1">Students</p>
            </div>
            <div>
              <p className="text-3xl font-bold">80+</p>
              <p className="text-xs opacity-70 mt-1">Teachers</p>
            </div>
            <div>
              <p className="text-3xl font-bold">20+</p>
              <p className="text-xs opacity-70 mt-1">Years</p>
            </div>
          </div>
        </div>
      </div>

      {/* Right panel - login form */}
      <div className="w-full lg:w-1/2 flex items-center justify-center bg-background px-4 py-8">
        <div className="w-full max-w-md space-y-6">
          {/* Mobile branding */}
          <div className="lg:hidden text-center space-y-2">
            <div className="bg-primary rounded-xl p-3 w-14 h-14 flex items-center justify-center mx-auto">
              <GraduationCap className="h-7 w-7 text-primary-foreground" />
            </div>
            <h1 className="text-xl font-bold">{schoolInfo.name}</h1>
          </div>

          <div className="space-y-1">
            <h2 className="text-2xl font-bold">Welcome back</h2>
            <p className="text-sm text-muted-foreground">Sign in to access the staff portal</p>
          </div>

          {/* Role tabs */}
          <div className="flex gap-2 p-1 bg-muted rounded-lg">
            <button
              type="button"
              className={`flex-1 py-2.5 rounded-md text-sm font-medium transition-all ${
                role === "admin"
                  ? "bg-card text-foreground shadow-sm"
                  : "text-muted-foreground hover:text-foreground"
              }`}
              onClick={() => setRole("admin")}
            >
              <ShieldCheck className="h-4 w-4 inline mr-1.5" />Admin
            </button>
            <button
              type="button"
              className={`flex-1 py-2.5 rounded-md text-sm font-medium transition-all ${
                role === "teacher"
                  ? "bg-card text-foreground shadow-sm"
                  : "text-muted-foreground hover:text-foreground"
              }`}
              onClick={() => setRole("teacher")}
            >
              <User className="h-4 w-4 inline mr-1.5" />Teacher
            </button>
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div className="space-y-1.5">
              <Label>Email Address</Label>
              <Input
                type="email"
                placeholder="you@school.com"
                required
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="h-11"
              />
            </div>
            <div className="space-y-1.5">
              <div className="flex items-center justify-between">
                <Label>Password</Label>
                <button type="button" className="text-xs text-primary hover:underline">Forgot password?</button>
              </div>
              <div className="relative">
                <Input
                  type={showPassword ? "text" : "password"}
                  placeholder="••••••••"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="h-11 pr-10"
                />
                <button
                  type="button"
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                  onClick={() => setShowPassword(!showPassword)}
                >
                  {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                </button>
              </div>
            </div>
            <Button type="submit" className="w-full h-11" disabled={loading}>
              {loading ? (
                <span className="flex items-center gap-2"><span className="h-4 w-4 border-2 border-primary-foreground/30 border-t-primary-foreground rounded-full animate-spin" /> Signing in...</span>
              ) : (
                <><LogIn className="h-4 w-4 mr-2" /> Sign In</>
              )}
            </Button>
          </form>

          {/* Demo Credentials */}
          <div className="rounded-lg border border-border bg-muted/50 p-4 space-y-3">
            <div className="flex items-center gap-2 text-sm font-medium text-muted-foreground">
              <Info className="h-4 w-4" />
              Quick Demo Access
            </div>
            <div className="space-y-2">
              {DEMO_CREDENTIALS.map((cred) => (
                <button
                  key={cred.email}
                  type="button"
                  onClick={() => handleDemoFill(cred)}
                  className="w-full flex items-center gap-3 rounded-md border border-border bg-background px-3 py-2.5 text-left text-sm transition-colors hover:bg-accent/10 hover:border-primary/30"
                >
                  <cred.icon className="h-4 w-4 shrink-0 text-primary" />
                  <div className="flex-1 min-w-0">
                    <span className="font-medium">{cred.role}</span>
                    <span className="mx-2 text-muted-foreground">·</span>
                    <span className="text-muted-foreground truncate">{cred.email}</span>
                  </div>
                  <span className="text-xs text-muted-foreground font-mono">{cred.password}</span>
                </button>
              ))}
            </div>
            <p className="text-xs text-muted-foreground">Click to auto-fill credentials.</p>
          </div>

          <div className="text-center">
            <Link to="/" className="inline-flex items-center gap-1.5 text-sm text-muted-foreground hover:text-primary transition-colors">
              <ArrowLeft className="h-3.5 w-3.5" /> Back to School Website
            </Link>
          </div>

          {/* Footer */}
          <p className="text-center text-xs text-muted-foreground">
            Powered by <span className="font-semibold text-foreground">JNV Tech</span> — JSchoolAdmin
          </p>
        </div>
      </div>
    </div>
  );
}
