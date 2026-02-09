// ============ Enhanced Teacher Types ============

export interface TeacherRecord {
  id: number;
  employee_id: string;
  photo_url?: string;
  full_name: string;
  gender: "male" | "female" | "other";
  dob: string;
  phone: string;
  whatsapp_number: string;
  email: string;
  address: string;
  qualification: string;
  experience_years: number;
  joining_date: string;
  subjects: string[];
  assigned_classes: string[]; // e.g. ["10-A", "9-B"]
  employment_type: "full-time" | "part-time";
  status: "active" | "inactive";

  created_at: string;
  updated_at?: string;
}

export interface TeacherDocument {
  id: number;
  teacher_id: number;
  name: string;
  type: "id_proof" | "certificate" | "resume" | "appointment_letter" | "other";
  file_url: string;
  file_size: string;
  uploaded_by: string;
  uploaded_at: string;
}

export interface TeacherMessage {
  id: number;
  teacher_id: number;
  template: string;
  message: string;
  sent_by: string;
  sent_at: string;
  channel: "whatsapp";
}

export interface TeacherMessageTemplate {
  id: string;
  title: string;
  body: string;
  category: "meeting" | "timetable" | "circular" | "emergency";
}

export const TEACHER_MESSAGE_TEMPLATES: TeacherMessageTemplate[] = [
  {
    id: "meeting",
    title: "Meeting Notice",
    body: "Dear {teacher_name}, you are requested to attend a staff meeting on {date} at {time}. Your presence is mandatory. - {school_name}",
    category: "meeting",
  },
  {
    id: "timetable",
    title: "Timetable Update",
    body: "Dear {teacher_name}, please note that your timetable has been updated for the current week. Kindly check the updated schedule at the earliest. - {school_name}",
    category: "timetable",
  },
  {
    id: "circular",
    title: "Circular",
    body: "Dear {teacher_name}, a new circular has been issued regarding {subject}. Please review it and acknowledge. - {school_name}",
    category: "circular",
  },
  {
    id: "emergency",
    title: "Emergency Alert",
    body: "URGENT: Dear {teacher_name}, due to {reason}, all classes are suspended for {date}. Please acknowledge this message immediately. - {school_name}",
    category: "emergency",
  },
];

export const SUBJECTS = [
  "Mathematics", "Science", "English", "Hindi", "Social Science",
  "Physics", "Chemistry", "Biology", "Computer Science", "Sanskrit",
  "Physical Education", "Art & Craft", "Music", "Economics", "Commerce",
];

export const QUALIFICATIONS = [
  "B.Ed", "M.Ed", "B.Sc + B.Ed", "M.Sc + B.Ed", "B.A + B.Ed",
  "M.A + B.Ed", "Ph.D", "D.El.Ed", "M.Phil", "MBA",
];
