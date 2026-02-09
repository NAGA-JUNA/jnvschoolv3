import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { GraduationCap, Send, CheckCircle } from "lucide-react";

export default function PublicAdmissions() {
  const [submitted, setSubmitted] = useState(false);

  if (submitted) {
    return (
      <div className="max-w-xl mx-auto py-20 px-4 text-center space-y-4">
        <div className="bg-success/10 w-20 h-20 rounded-full flex items-center justify-center mx-auto">
          <CheckCircle className="h-10 w-10 text-success" />
        </div>
        <h2 className="text-2xl font-bold">Application Submitted!</h2>
        <p className="text-muted-foreground">
          Thank you for your application. We'll review it and get back to you via email or phone.
        </p>
        <Button onClick={() => setSubmitted(false)} variant="outline">Submit Another</Button>
      </div>
    );
  }

  return (
    <div className="max-w-2xl mx-auto py-10 px-4 space-y-6">
      <div className="text-center space-y-2">
        <div className="bg-primary/10 w-16 h-16 rounded-full flex items-center justify-center mx-auto">
          <GraduationCap className="h-8 w-8 text-primary" />
        </div>
        <h1 className="text-3xl font-bold">Online Admission Form</h1>
        <p className="text-muted-foreground">Fill out the form below to apply for admission</p>
      </div>

      <Card className="p-6">
        <form onSubmit={(e) => { e.preventDefault(); setSubmitted(true); }} className="space-y-5">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><Label>Student Name *</Label><Input placeholder="Full name" required /></div>
            <div><Label>Class Applying For *</Label>
              <Select required>
                <SelectTrigger><SelectValue placeholder="Select class" /></SelectTrigger>
                <SelectContent>
                  {["Nursery","LKG","UKG","1","2","3","4","5","6","7","8","9","10","11","12"].map((c) => (
                    <SelectItem key={c} value={c}>{c === "Nursery" || c === "LKG" || c === "UKG" ? c : `Class ${c}`}</SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>
            <div><Label>Date of Birth *</Label><Input type="date" required /></div>
            <div><Label>Gender *</Label>
              <Select required>
                <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                <SelectContent>
                  <SelectItem value="male">Male</SelectItem>
                  <SelectItem value="female">Female</SelectItem>
                  <SelectItem value="other">Other</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div><Label>Parent/Guardian Name *</Label><Input placeholder="Parent name" required /></div>
            <div><Label>Phone Number *</Label><Input type="tel" placeholder="Phone" required /></div>
            <div><Label>WhatsApp Number *</Label><Input type="tel" placeholder="WhatsApp" required /></div>
            <div><Label>Email (Optional)</Label><Input type="email" placeholder="Email" /></div>
            <div className="md:col-span-2"><Label>Address *</Label><Textarea placeholder="Full address" required rows={2} /></div>
            <div className="md:col-span-2"><Label>Previous School (if any)</Label><Input placeholder="Previous school name" /></div>
          </div>
          <Button type="submit" className="w-full" size="lg"><Send className="h-4 w-4 mr-2" /> Submit Application</Button>
        </form>
      </Card>
    </div>
  );
}
