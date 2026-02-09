// ============================================
// JSchoolAdmin — API Endpoint Definitions
// Version: 1.1.0
// ============================================

// Authentication endpoints
export const AUTH = {
  login: "/auth/login",
  logout: "/auth/logout",
  me: "/auth/me",
} as const;

// Public API endpoints (no auth required)
export const PUBLIC = {
  notifications: "/public/notifications",
  galleryCategories: "/public/gallery/categories",
  galleryItems: (slug: string) => `/public/gallery/items?category=${slug}`,
  events: "/public/events",
  admissions: "/public/admissions",
} as const;

// Admin API endpoints (requires admin role)
export const ADMIN = {
  // Dashboard
  dashboard: "/admin/dashboard/metrics",
  activity: "/admin/dashboard/activity",
  alerts: "/admin/alerts",

  // Students
  students: "/admin/students",
  student: (id: number) => `/admin/students/${id}`,
  studentDelete: (id: number) => `/admin/students/${id}`,
  studentsExport: "/admin/students/export",
  studentsImport: "/admin/students/import",
  studentsBulkPromote: "/admin/students/bulk-promote",
  studentDocuments: (id: number) => `/admin/students/${id}/documents`,
  studentAttendance: (id: number) => `/admin/students/${id}/attendance`,
  studentExams: (id: number) => `/admin/students/${id}/exams`,
  studentMessages: (id: number) => `/admin/students/${id}/messages`,
  alumni: "/admin/students/alumni",

  // Teachers
  teachers: "/admin/teachers",
  teacher: (id: number) => `/admin/teachers/${id}`,
  teacherDelete: (id: number) => `/admin/teachers/${id}`,
  teachersExport: "/admin/teachers/export",
  teachersImport: "/admin/teachers/import",
  teacherDocuments: (id: number) => `/admin/teachers/${id}/documents`,
  teacherMessages: (id: number) => `/admin/teachers/${id}/messages`,
  teacherAttendance: (id: number) => `/admin/teachers/${id}/attendance`,
  teacherAssignClasses: (id: number) => `/admin/teachers/${id}/assign-classes`,
  inactiveTeachers: "/admin/teachers/inactive",

  // Admissions
  admissions: "/admin/admissions",
  admission: (id: number) => `/admin/admissions/${id}`,
  admissionsExport: "/admin/admissions/export",

  // Notifications
  notifications: "/admin/notifications",
  notification: (id: number) => `/admin/notifications/${id}`,
  notificationApprove: (id: number) => `/admin/notifications/${id}/approve`,
  notificationReject: (id: number) => `/admin/notifications/${id}/reject`,
  notificationBulkApprove: "/admin/notifications/bulk-approve",

  // Gallery
  galleryCategories: "/admin/gallery/categories",
  galleryCategory: (id: number) => `/admin/gallery/categories/${id}`,
  galleryApprovals: "/admin/gallery/approvals",
  galleryApprove: (id: number) => `/admin/gallery/items/${id}/approve`,
  galleryReject: (id: number) => `/admin/gallery/items/${id}/reject`,

  // Events
  events: "/admin/events",
  event: (id: number) => `/admin/events/${id}`,

  // WhatsApp
  whatsappGroups: "/admin/whatsapp/groups",
  whatsappLogs: "/admin/whatsapp/logs",
  whatsappLog: "/admin/whatsapp/log",

  // Email
  emails: "/admin/emails",
  emailCreate: "/admin/emails/create",

  // Reports & Logs
  reports: "/admin/reports",
  auditLogs: "/admin/audit-logs",

  // Settings & Branding
  settings: "/admin/settings",
  branding: "/admin/branding",
} as const;

// Teacher API endpoints (requires teacher/office role)
export const TEACHER = {
  dashboard: "/teacher/dashboard/metrics",
  activity: "/teacher/dashboard/activity",
  postNotification: "/teacher/notifications",
  uploadGallery: "/teacher/gallery/upload",
  addYoutubeLink: "/teacher/gallery/youtube",
  mySubmissions: "/teacher/submissions",
  profile: "/teacher/profile",
  students: "/teacher/students",
  markAttendance: "/teacher/attendance/mark",
  enterMarks: "/teacher/exams/marks",
} as const;
