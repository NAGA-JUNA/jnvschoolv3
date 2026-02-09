import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { GraduationCap, LogIn } from "lucide-react";
import { Link, useNavigate } from "react-router-dom";

export default function LoginPage() {
  const navigate = useNavigate();
  const [role, setRole] = useState<"admin" | "teacher">("admin");

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    navigate(role === "admin" ? "/admin" : "/teacher");
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
          <div><Label>Email</Label><Input type="email" placeholder="you@school.com" required /></div>
          <div><Label>Password</Label><Input type="password" placeholder="••••••••" required /></div>
          <Button type="submit" className="w-full" size="lg"><LogIn className="h-4 w-4 mr-2" /> Sign In</Button>
        </form>

        <div className="text-center">
          <Link to="/" className="text-sm text-primary hover:underline">← Back to Website</Link>
        </div>
      </Card>
    </div>
  );
}
