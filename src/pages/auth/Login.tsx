import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { GraduationCap, LogIn, Info, User, ShieldCheck } from "lucide-react";
import { Link, useNavigate } from "react-router-dom";

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

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // In demo mode, navigate based on selected role
    // When backend is connected, this will POST to /auth/login
    navigate(role === "admin" ? "/admin" : "/teacher");
  };

  const handleDemoFill = (cred: typeof DEMO_CREDENTIALS[number]) => {
    setEmail(cred.email);
    setPassword(cred.password);
    setRole(cred.nav === "/admin" ? "admin" : "teacher");
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-background px-4">
      <Card className="w-full max-w-md p-8 space-y-6">
        <div className="text-center space-y-2">
          <div className="bg-primary rounded-xl p-3 w-14 h-14 flex items-center justify-center mx-auto">
            <GraduationCap className="h-7 w-7 text-primary-foreground" />
          </div>
          <h1 className="text-2xl font-bold">Staff Login</h1>
          <p className="text-sm text-muted-foreground">Sign in to access the admin panel</p>
        </div>

        <div className="flex gap-2">
          <Button
            variant={role === "admin" ? "default" : "outline"}
            className="flex-1"
            onClick={() => setRole("admin")}
          >
            Admin
          </Button>
          <Button
            variant={role === "teacher" ? "default" : "outline"}
            className="flex-1"
            onClick={() => setRole("teacher")}
          >
            Teacher
          </Button>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <Label>Email</Label>
            <Input
              type="email"
              placeholder="you@school.com"
              required
              value={email}
              onChange={(e) => setEmail(e.target.value)}
            />
          </div>
          <div>
            <Label>Password</Label>
            <Input
              type="password"
              placeholder="••••••••"
              required
              value={password}
              onChange={(e) => setPassword(e.target.value)}
            />
          </div>
          <Button type="submit" className="w-full" size="lg">
            <LogIn className="h-4 w-4 mr-2" /> Sign In
          </Button>
        </form>

        {/* Demo Credentials */}
        <div className="rounded-lg border border-border bg-muted/50 p-4 space-y-3">
          <div className="flex items-center gap-2 text-sm font-medium text-muted-foreground">
            <Info className="h-4 w-4" />
            Demo Credentials
          </div>
          <div className="space-y-2">
            {DEMO_CREDENTIALS.map((cred) => (
              <button
                key={cred.email}
                type="button"
                onClick={() => handleDemoFill(cred)}
                className="w-full flex items-center gap-3 rounded-md border border-border bg-background px-3 py-2.5 text-left text-sm transition-colors hover:bg-accent hover:text-accent-foreground"
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
          <p className="text-xs text-muted-foreground">
            Click any row to auto-fill credentials. These are for testing only.
          </p>
        </div>

        <div className="text-center">
          <Link to="/" className="text-sm text-primary hover:underline">← Back to Website</Link>
        </div>
      </Card>
    </div>
  );
}
