// ============ Auth & Users ============
export type UserRole = "ADMIN" | "TEACHER" | "OFFICE";

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  phone?: string;
  whatsapp?: string;
  subject?: string;
  status: "active" | "inactive";
  created_at: string;
}

// ============ Dashboard ============
export interface DashboardMetrics {
  total_students: number;
  total_teachers: number;
  pending_notifications: number;
  pending_gallery: number;
  new_admissions: number;
}

export interface ActivityItem {
  id: number;
  type: "notification" | "gallery" | "admission" | "student" | "teacher" | "event";
  message: string;
  timestamp: string;
  link?: string;
}

export interface ChartDataPoint {
  month: string;
  admissions: number;
  notifications: number;
  gallery: number;
}

export interface SystemAlert {
  id: number;
  message: string;
  type: "info" | "warning" | "error";
  active: boolean;
}

// ============ Students ============
export interface Student {
  id: number;
  name: string;
  class: string;
  section: string;
  roll_no: string;
  gender: "male" | "female" | "other";
  dob: string;
  parent_name: string;
  parent_phone: string;
  parent_whatsapp: string;
  address: string;
  status: "active" | "inactive" | "transferred";
  created_at: string;
}

// ============ Teachers ============
export interface Teacher {
  id: number;
  name: string;
  email: string;
  phone: string;
  whatsapp: string;
  subject: string;
  status: "active" | "inactive";
  created_at: string;
}

// ============ Notifications ============
export type UrgencyLevel = "normal" | "important" | "urgent";
export type ApprovalStatus = "pending" | "approved" | "rejected";

export interface Notification {
  id: number;
  title: string;
  body: string;
  urgency: UrgencyLevel;
  expiry: string;
  attachment_url?: string;
  attachment_type?: string;
  status: ApprovalStatus;
  rejection_reason?: string;
  submitted_by: number;
  submitted_by_name?: string;
  created_at: string;
}

// ============ Gallery ============
export type CategoryType = "images" | "videos";

export interface GalleryCategory {
  id: number;
  name: string;
  slug: string;
  type: CategoryType;
  item_count?: number;
  created_at: string;
}

export interface GalleryItem {
  id: number;
  category_id: number;
  category_name?: string;
  type: "image" | "youtube";
  url: string;
  thumbnail_url?: string;
  title?: string;
  status: ApprovalStatus;
  submitted_by: number;
  submitted_by_name?: string;
  created_at: string;
}

// ============ Events ============
export interface SchoolEvent {
  id: number;
  title: string;
  description: string;
  date: string;
  end_date?: string;
  location?: string;
  type: "academic" | "cultural" | "sports" | "holiday" | "other";
  created_at: string;
}

// ============ Admissions ============
export type AdmissionStatus = "new" | "approved" | "rejected";

export interface Admission {
  id: number;
  student_name: string;
  class_applied: string;
  parent_name: string;
  parent_phone: string;
  parent_email?: string;
  parent_whatsapp: string;
  address: string;
  dob: string;
  gender: "male" | "female" | "other";
  previous_school?: string;
  status: AdmissionStatus;
  notes?: string;
  created_at: string;
}

// ============ WhatsApp ============
export interface WhatsAppShareLog {
  id: number;
  item_type: "notification" | "event" | "admission";
  item_id: number;
  item_title: string;
  shared_by: number;
  shared_at: string;
}

export interface WhatsAppGroup {
  id: number;
  name: string;
  link: string;
}

// ============ Email Management ============
export interface OfficialEmail {
  id: number;
  user_id: number;
  user_name: string;
  email: string;
  status: "active" | "suspended";
  created_at: string;
}

// ============ Audit Logs ============
export interface AuditLog {
  id: number;
  user_id: number;
  user_name: string;
  action: string;
  target_type: string;
  target_id?: number;
  details?: string;
  ip_address?: string;
  created_at: string;
}

// ============ API ============
export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message?: string;
  pagination?: {
    page: number;
    per_page: number;
    total: number;
    total_pages: number;
  };
}

export interface ApiError {
  success: false;
  message: string;
  errors?: Record<string, string[]>;
}
