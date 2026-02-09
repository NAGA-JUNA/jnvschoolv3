import { useState } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { schoolInfo } from "@/data/mockSchoolData";
import { Phone, Mail, MapPin, Clock, Send, CheckCircle } from "lucide-react";
import { toast } from "@/hooks/use-toast";

export default function ContactPage() {
  const [submitted, setSubmitted] = useState(false);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // In demo mode just show success — backend will handle actual email
    setSubmitted(true);
    toast({ title: "Message Sent!", description: "We'll get back to you within 24 hours." });
  };

  return (
    <div>
      {/* Hero */}
      <section className="bg-gradient-to-br from-primary to-primary/80 text-primary-foreground py-16 px-4">
        <div className="max-w-4xl mx-auto text-center space-y-3">
          <h1 className="text-3xl md:text-4xl font-bold tracking-tight">Contact Us</h1>
          <p className="text-lg opacity-90">We'd love to hear from you</p>
        </div>
      </section>

      <div className="max-w-6xl mx-auto py-14 px-4">
        <div className="grid lg:grid-cols-3 gap-8">
          {/* Contact Cards */}
          <div className="space-y-5">
            <Card className="p-5 flex items-start gap-4">
              <div className="bg-primary/10 rounded-lg p-2.5 shrink-0">
                <Phone className="h-5 w-5 text-primary" />
              </div>
              <div>
                <h3 className="font-semibold">Phone</h3>
                <p className="text-sm text-muted-foreground mt-1">{schoolInfo.phone}</p>
                <p className="text-sm text-muted-foreground">{schoolInfo.altPhone}</p>
              </div>
            </Card>

            <Card className="p-5 flex items-start gap-4">
              <div className="bg-primary/10 rounded-lg p-2.5 shrink-0">
                <Mail className="h-5 w-5 text-primary" />
              </div>
              <div>
                <h3 className="font-semibold">Email</h3>
                <p className="text-sm text-muted-foreground mt-1">{schoolInfo.email}</p>
              </div>
            </Card>

            <Card className="p-5 flex items-start gap-4">
              <div className="bg-primary/10 rounded-lg p-2.5 shrink-0">
                <MapPin className="h-5 w-5 text-primary" />
              </div>
              <div>
                <h3 className="font-semibold">Address</h3>
                <p className="text-sm text-muted-foreground mt-1">{schoolInfo.address}</p>
              </div>
            </Card>

            <Card className="p-5 flex items-start gap-4">
              <div className="bg-primary/10 rounded-lg p-2.5 shrink-0">
                <Clock className="h-5 w-5 text-primary" />
              </div>
              <div>
                <h3 className="font-semibold">Office Hours</h3>
                <div className="text-sm text-muted-foreground mt-1 space-y-0.5">
                  <p>Mon – Fri: {schoolInfo.officeHours.weekdays}</p>
                  <p>Saturday: {schoolInfo.officeHours.saturday}</p>
                  <p>Sunday: {schoolInfo.officeHours.sunday}</p>
                </div>
              </div>
            </Card>
          </div>

          {/* Contact Form */}
          <div className="lg:col-span-2">
            <Card className="p-6 md:p-8">
              <h2 className="text-xl font-bold mb-6">Send us a Message</h2>

              {submitted ? (
                <div className="text-center py-12 space-y-4">
                  <CheckCircle className="h-16 w-16 text-success mx-auto" />
                  <h3 className="text-xl font-bold">Thank You!</h3>
                  <p className="text-muted-foreground">Your message has been sent. We will respond within 24 hours.</p>
                  <Button variant="outline" onClick={() => setSubmitted(false)}>
                    Send Another Message
                  </Button>
                </div>
              ) : (
                <form onSubmit={handleSubmit} className="space-y-5">
                  <div className="grid md:grid-cols-2 gap-4">
                    <div>
                      <Label>Full Name *</Label>
                      <Input placeholder="Your full name" required />
                    </div>
                    <div>
                      <Label>Phone Number</Label>
                      <Input placeholder="+91-XXXXXXXXXX" type="tel" />
                    </div>
                  </div>
                  <div>
                    <Label>Email Address *</Label>
                    <Input placeholder="you@example.com" type="email" required />
                  </div>
                  <div>
                    <Label>Subject *</Label>
                    <Input placeholder="What is this about?" required />
                  </div>
                  <div>
                    <Label>Message *</Label>
                    <Textarea placeholder="Write your message here..." rows={5} required />
                  </div>
                  <Button type="submit" size="lg" className="w-full sm:w-auto">
                    <Send className="h-4 w-4 mr-2" /> Send Message
                  </Button>
                </form>
              )}
            </Card>
          </div>
        </div>

        {/* Map */}
        <div className="mt-10 rounded-xl overflow-hidden border">
          <iframe
            src={schoolInfo.mapUrl}
            width="100%"
            height="350"
            style={{ border: 0 }}
            allowFullScreen
            loading="lazy"
            referrerPolicy="no-referrer-when-downgrade"
            title="School Location"
          />
        </div>
      </div>
    </div>
  );
}
