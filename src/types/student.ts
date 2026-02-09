// ============ Enhanced Student Types ============

export interface StudentRecord {
  id: number;
  student_id: string;
  photo_url?: string;
  full_name: string;
  gender: "male" | "female" | "other";
  dob: string;
  admission_no: string;
  roll_no: string;
  class: string;
  section: string;
  academic_year: string;
  status: "active" | "inactive" | "alumni";

  // Parent / Guardian
  father_name: string;
  mother_name: string;
  primary_phone: string;
  whatsapp_number: string;
  email?: string;
  address: string;
  emergency_contact?: string;

  created_at: string;
  updated_at?: string;
}

export interface AttendanceRecord {
  id: number;
  student_id: number;
  date: string;
  status: "present" | "absent" | "late" | "half_day";
  marked_by: string;
  remarks?: string;
}

export interface AttendanceSummary {
  month: string;
  present: number;
  absent: number;
  late: number;
  half_day: number;
  total_days: number;
}

export interface ExamResult {
  id: number;
  student_id: number;
  exam_name: string;
  subject: string;
  max_marks: number;
  obtained_marks: number;
  grade: string;
  remarks?: string;
  exam_date: string;
}

export interface StudentDocument {
  id: number;
  student_id: number;
  name: string;
  type: "aadhaar" | "birth_certificate" | "transfer_certificate" | "photo" | "other";
  file_url: string;
  file_size: string;
  uploaded_by: string;
  uploaded_at: string;
}

export interface StudentMessage {
  id: number;
  student_id: number;
  template: string;
  message: string;
  sent_by: string;
  sent_at: string;
  channel: "whatsapp";
}

export interface MessageTemplate {
  id: string;
  title: string;
  body: string;
  category: "absentee" | "exam" | "event" | "fee";
}

export const MESSAGE_TEMPLATES: MessageTemplate[] = [
  {
    id: "absentee",
    title: "Absentee Alert",
    body: "Dear Parent, this is to inform you that your ward {student_name} of Class {class}-{section} was absent on {date}. Please ensure regular attendance. - {school_name}",
    category: "absentee",
  },
  {
    id: "exam",
    title: "Exam Information",
    body: "Dear Parent, the upcoming exams for Class {class} will commence from {date}. Please ensure your ward {student_name} is well prepared. - {school_name}",
    category: "exam",
  },
  {
    id: "event",
    title: "Event Notice",
    body: "Dear Parent, you are cordially invited to {event_name} on {date} at {school_name}. Your ward {student_name} is participating. We look forward to your presence.",
    category: "event",
  },
  {
    id: "fee",
    title: "Fee Reminder",
    body: "Dear Parent, this is a gentle reminder that the fee for your ward {student_name} of Class {class}-{section} is pending. Please clear the dues at the earliest. - {school_name}",
    category: "fee",
  },
];

export const CLASSES = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"];
export const SECTIONS = ["A", "B", "C", "D"];
export const ACADEMIC_YEARS = ["2024-25", "2025-26", "2026-27"];
