import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { ThemeProvider } from "@/contexts/ThemeContext";

// Layouts
import { PanelLayout } from "@/components/layout/PanelLayout";
import { PublicLayout } from "@/components/layout/PublicLayout";

// Auth
import LoginPage from "@/pages/auth/Login";

// Public pages
import PublicHome from "@/pages/public/Home";
import PublicAbout from "@/pages/public/About";
import PublicAcademics from "@/pages/public/Academics";
import PublicFaculty from "@/pages/public/Faculty";
import PublicNotifications from "@/pages/public/Notifications";
import PublicGallery from "@/pages/public/Gallery";
import PublicEvents from "@/pages/public/Events";
import PublicAdmissions from "@/pages/public/Admissions";
import PublicContact from "@/pages/public/Contact";
import DeveloperPage from "@/pages/public/Developer";

// Admin pages
import AdminDashboard from "@/pages/admin/Dashboard";
import StudentsPage from "@/pages/admin/students/StudentsList";
import TeachersListPage from "@/pages/admin/teachers/TeachersList";
import TeacherFormPage from "@/pages/admin/teachers/TeacherForm";
import TeacherProfilePage from "@/pages/admin/teachers/TeacherProfile";
import ImportTeachersPage from "@/pages/admin/teachers/ImportTeachers";
import InactiveTeachersPage from "@/pages/admin/teachers/InactiveTeachers";
import StudentFormPage from "@/pages/admin/students/StudentForm";
import StudentProfilePage from "@/pages/admin/students/StudentProfile";
import AlumniPage from "@/pages/admin/students/AlumniPage";
import ImportStudentsPage from "@/pages/admin/students/ImportStudents";
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
import BrandingSettingsPage from "@/pages/admin/BrandingSettings";
import HomeBannerPage from "@/pages/admin/HomeBanner";
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
    <ThemeProvider>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <Routes>
          {/* Public Routes */}
          <Route element={<PublicLayout />}>
            <Route path="/" element={<PublicHome />} />
            <Route path="/about" element={<PublicAbout />} />
            <Route path="/academics" element={<PublicAcademics />} />
            <Route path="/faculty" element={<PublicFaculty />} />
            <Route path="/notifications" element={<PublicNotifications />} />
            <Route path="/gallery" element={<PublicGallery />} />
            <Route path="/events" element={<PublicEvents />} />
            <Route path="/admissions" element={<PublicAdmissions />} />
            <Route path="/contact" element={<PublicContact />} />
          </Route>

          {/* Auth */}
          <Route path="/login" element={<LoginPage />} />

          {/* Admin Panel */}
          <Route element={<PanelLayout role="admin" />}>
            <Route path="/admin" element={<AdminDashboard />} />
            <Route path="/admin/teachers" element={<TeachersListPage />} />
            <Route path="/admin/teachers/add" element={<TeacherFormPage />} />
            <Route path="/admin/teachers/import" element={<ImportTeachersPage />} />
            <Route path="/admin/teachers/inactive" element={<InactiveTeachersPage />} />
            <Route path="/admin/teachers/:id" element={<TeacherProfilePage />} />
            <Route path="/admin/teachers/:id/edit" element={<TeacherFormPage />} />
            <Route path="/admin/students" element={<StudentsPage />} />
            <Route path="/admin/students/add" element={<StudentFormPage />} />
            <Route path="/admin/students/import" element={<ImportStudentsPage />} />
            <Route path="/admin/students/alumni" element={<AlumniPage />} />
            <Route path="/admin/students/:id" element={<StudentProfilePage />} />
            <Route path="/admin/students/:id/edit" element={<StudentFormPage />} />
            <Route path="/admin/admissions" element={<AdmissionsPage />} />
            <Route path="/admin/notifications" element={<NotificationsPage />} />
            <Route path="/admin/gallery-categories" element={<GalleryCategoriesPage />} />
            <Route path="/admin/gallery-approvals" element={<GalleryApprovalsPage />} />
            <Route path="/admin/events" element={<EventsPage />} />
            <Route path="/admin/whatsapp" element={<WhatsAppManualPage />} />
            <Route path="/admin/emails" element={<EmailManagementPage />} />
            <Route path="/admin/reports" element={<ReportsPage />} />
            <Route path="/admin/audit" element={<AuditLogsPage />} />
            <Route path="/admin/branding" element={<BrandingSettingsPage />} />
            <Route path="/admin/home-banner" element={<HomeBannerPage />} />
            <Route path="/admin/settings" element={<SettingsPage />} />
            <Route path="/admin/developer" element={<DeveloperPage />} />
          </Route>

          {/* Teacher Panel */}
          <Route element={<PanelLayout role="teacher" />}>
            <Route path="/teacher" element={<TeacherDashboard />} />
            <Route path="/teacher/post-notification" element={<PostNotification />} />
            <Route path="/teacher/upload-gallery" element={<UploadGallery />} />
            <Route path="/teacher/submissions" element={<MySubmissions />} />
            <Route path="/teacher/profile" element={<TeacherProfile />} />
            <Route path="/teacher/developer" element={<DeveloperPage />} />
          </Route>

          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
    </ThemeProvider>
  </QueryClientProvider>
);

export default App;
