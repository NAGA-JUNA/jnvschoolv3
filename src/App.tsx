import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";

// Layouts
import { PanelLayout } from "@/components/layout/PanelLayout";
import { PublicLayout } from "@/components/layout/PublicLayout";

// Auth
import LoginPage from "@/pages/auth/Login";

// Public pages
import PublicHome from "@/pages/public/Home";
import PublicNotifications from "@/pages/public/Notifications";
import PublicGallery from "@/pages/public/Gallery";
import PublicEvents from "@/pages/public/Events";
import PublicAdmissions from "@/pages/public/Admissions";

// Admin pages
import AdminDashboard from "@/pages/admin/Dashboard";
import TeachersPage from "@/pages/admin/Teachers";
import StudentsPage from "@/pages/admin/Students";
import AdmissionsPage from "@/pages/admin/Admissions";
import NotificationsPage from "@/pages/admin/Notifications";
import GalleryCategoriesPage from "@/pages/admin/GalleryCategories";
import GalleryApprovalsPage from "@/pages/admin/GalleryApprovals";
import EventsPage from "@/pages/admin/Events";
import WhatsAppManualPage from "@/pages/admin/WhatsAppManual";
import EmailManagementPage from "@/pages/admin/EmailManagement";
import ReportsPage from "@/pages/admin/Reports";
import AuditLogsPage from "@/pages/admin/AuditLogs";
import SettingsPage from "@/pages/admin/Settings";

// Teacher pages
import TeacherDashboard from "@/pages/teacher/Dashboard";
import PostNotification from "@/pages/teacher/PostNotification";
import UploadGallery from "@/pages/teacher/UploadGallery";
import MySubmissions from "@/pages/teacher/MySubmissions";
import TeacherProfile from "@/pages/teacher/Profile";

import NotFound from "@/pages/NotFound";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <Routes>
          {/* Public Routes */}
          <Route element={<PublicLayout />}>
            <Route path="/" element={<PublicHome />} />
            <Route path="/notifications" element={<PublicNotifications />} />
            <Route path="/gallery" element={<PublicGallery />} />
            <Route path="/events" element={<PublicEvents />} />
            <Route path="/admissions" element={<PublicAdmissions />} />
          </Route>

          {/* Auth */}
          <Route path="/login" element={<LoginPage />} />

          {/* Admin Panel */}
          <Route element={<PanelLayout role="admin" />}>
            <Route path="/admin" element={<AdminDashboard />} />
            <Route path="/admin/teachers" element={<TeachersPage />} />
            <Route path="/admin/students" element={<StudentsPage />} />
            <Route path="/admin/admissions" element={<AdmissionsPage />} />
            <Route path="/admin/notifications" element={<NotificationsPage />} />
            <Route path="/admin/gallery-categories" element={<GalleryCategoriesPage />} />
            <Route path="/admin/gallery-approvals" element={<GalleryApprovalsPage />} />
            <Route path="/admin/events" element={<EventsPage />} />
            <Route path="/admin/whatsapp" element={<WhatsAppManualPage />} />
            <Route path="/admin/emails" element={<EmailManagementPage />} />
            <Route path="/admin/reports" element={<ReportsPage />} />
            <Route path="/admin/audit" element={<AuditLogsPage />} />
            <Route path="/admin/settings" element={<SettingsPage />} />
          </Route>

          {/* Teacher Panel */}
          <Route element={<PanelLayout role="teacher" />}>
            <Route path="/teacher" element={<TeacherDashboard />} />
            <Route path="/teacher/post-notification" element={<PostNotification />} />
            <Route path="/teacher/upload-gallery" element={<UploadGallery />} />
            <Route path="/teacher/submissions" element={<MySubmissions />} />
            <Route path="/teacher/profile" element={<TeacherProfile />} />
          </Route>

          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
