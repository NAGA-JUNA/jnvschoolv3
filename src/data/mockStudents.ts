import { StudentRecord, AttendanceRecord, AttendanceSummary, ExamResult, StudentDocument, StudentMessage } from "@/types/student";

export const mockStudents: StudentRecord[] = [
  {
    id: 1, student_id: "STU-2025-001", photo_url: "", full_name: "Rahul Sharma", gender: "male", dob: "2009-05-15",
    admission_no: "ADM-1001", roll_no: "01", class: "10", section: "A", academic_year: "2025-26", status: "active",
    father_name: "Vikram Sharma", mother_name: "Sunita Sharma", primary_phone: "9876543220", whatsapp_number: "9876543220",
    email: "vikram.sharma@email.com", address: "123 Main Street, New Delhi", emergency_contact: "9876543221", created_at: "2024-01-10",
  },
  {
    id: 2, student_id: "STU-2025-002", photo_url: "", full_name: "Sneha Patel", gender: "female", dob: "2009-08-22",
    admission_no: "ADM-1002", roll_no: "02", class: "10", section: "B", academic_year: "2025-26", status: "active",
    father_name: "Ramesh Patel", mother_name: "Kavita Patel", primary_phone: "9876543221", whatsapp_number: "9876543221",
    email: "ramesh.patel@email.com", address: "456 Park Road, Mumbai", created_at: "2024-01-12",
  },
  {
    id: 3, student_id: "STU-2025-003", photo_url: "", full_name: "Arjun Singh", gender: "male", dob: "2010-03-08",
    admission_no: "ADM-0902", roll_no: "03", class: "9", section: "A", academic_year: "2025-26", status: "active",
    father_name: "Baldev Singh", mother_name: "Harpreet Kaur", primary_phone: "9876543222", whatsapp_number: "9876543222",
    address: "789 Oak Avenue, Chandigarh", created_at: "2023-06-15",
  },
  {
    id: 4, student_id: "STU-2025-004", photo_url: "", full_name: "Priya Gupta", gender: "female", dob: "2010-11-03",
    admission_no: "ADM-0801", roll_no: "04", class: "8", section: "A", academic_year: "2025-26", status: "active",
    father_name: "Anil Gupta", mother_name: "Reena Gupta", primary_phone: "9876543223", whatsapp_number: "9876543223",
    email: "anil.gupta@email.com", address: "321 Lake View, Jaipur", created_at: "2023-07-20",
  },
  {
    id: 5, student_id: "STU-2025-005", photo_url: "", full_name: "Amit Kumar", gender: "male", dob: "2008-12-28",
    admission_no: "ADM-1101", roll_no: "05", class: "11", section: "B", academic_year: "2025-26", status: "active",
    father_name: "Rajesh Kumar", mother_name: "Meena Kumar", primary_phone: "9876543224", whatsapp_number: "9876543224",
    address: "654 Green Park, Lucknow", created_at: "2024-03-01",
  },
  {
    id: 6, student_id: "STU-2025-006", photo_url: "", full_name: "Kavya Nair", gender: "female", dob: "2009-07-14",
    admission_no: "ADM-1003", roll_no: "06", class: "10", section: "A", academic_year: "2025-26", status: "inactive",
    father_name: "Suresh Nair", mother_name: "Lakshmi Nair", primary_phone: "9876543225", whatsapp_number: "9876543225",
    address: "987 Temple Road, Kochi", created_at: "2024-02-14",
  },
  {
    id: 7, student_id: "STU-2024-007", photo_url: "", full_name: "Deepak Verma", gender: "male", dob: "2007-04-19",
    admission_no: "ADM-1201", roll_no: "07", class: "12", section: "A", academic_year: "2024-25", status: "alumni",
    father_name: "Mahesh Verma", mother_name: "Anita Verma", primary_phone: "9876543226", whatsapp_number: "9876543226",
    address: "147 Hill View, Dehradun", created_at: "2022-04-01",
  },
  {
    id: 8, student_id: "STU-2024-008", photo_url: "", full_name: "Riya Joshi", gender: "female", dob: "2007-09-25",
    admission_no: "ADM-1202", roll_no: "08", class: "12", section: "B", academic_year: "2024-25", status: "alumni",
    father_name: "Prakash Joshi", mother_name: "Shalini Joshi", primary_phone: "9876543227", whatsapp_number: "9876543227",
    email: "prakash.joshi@email.com", address: "258 River Side, Patna", created_at: "2022-04-05",
  },
];

export const mockAttendance: AttendanceRecord[] = [
  { id: 1, student_id: 1, date: "2026-02-03", status: "present", marked_by: "Mrs. Sharma" },
  { id: 2, student_id: 1, date: "2026-02-04", status: "present", marked_by: "Mrs. Sharma" },
  { id: 3, student_id: 1, date: "2026-02-05", status: "absent", marked_by: "Mrs. Sharma", remarks: "Sick leave" },
  { id: 4, student_id: 1, date: "2026-02-06", status: "present", marked_by: "Mrs. Sharma" },
  { id: 5, student_id: 1, date: "2026-02-07", status: "late", marked_by: "Mrs. Sharma", remarks: "Late by 15 mins" },
];

export const mockAttendanceSummary: AttendanceSummary[] = [
  { month: "Sep", present: 22, absent: 2, late: 1, half_day: 0, total_days: 25 },
  { month: "Oct", present: 20, absent: 1, late: 2, half_day: 0, total_days: 23 },
  { month: "Nov", present: 19, absent: 3, late: 0, half_day: 1, total_days: 23 },
  { month: "Dec", present: 16, absent: 2, late: 1, half_day: 0, total_days: 19 },
  { month: "Jan", present: 23, absent: 1, late: 1, half_day: 0, total_days: 25 },
  { month: "Feb", present: 5, absent: 1, late: 1, half_day: 0, total_days: 7 },
];

export const mockExamResults: ExamResult[] = [
  { id: 1, student_id: 1, exam_name: "Mid-Term 2025", subject: "Mathematics", max_marks: 100, obtained_marks: 92, grade: "A+", exam_date: "2025-09-15" },
  { id: 2, student_id: 1, exam_name: "Mid-Term 2025", subject: "Science", max_marks: 100, obtained_marks: 88, grade: "A", exam_date: "2025-09-16" },
  { id: 3, student_id: 1, exam_name: "Mid-Term 2025", subject: "English", max_marks: 100, obtained_marks: 85, grade: "A", exam_date: "2025-09-17" },
  { id: 4, student_id: 1, exam_name: "Mid-Term 2025", subject: "Hindi", max_marks: 100, obtained_marks: 78, grade: "B+", exam_date: "2025-09-18" },
  { id: 5, student_id: 1, exam_name: "Mid-Term 2025", subject: "Social Science", max_marks: 100, obtained_marks: 82, grade: "A", exam_date: "2025-09-19" },
  { id: 6, student_id: 1, exam_name: "Annual 2025", subject: "Mathematics", max_marks: 100, obtained_marks: 95, grade: "A+", exam_date: "2026-02-01" },
  { id: 7, student_id: 1, exam_name: "Annual 2025", subject: "Science", max_marks: 100, obtained_marks: 91, grade: "A+", exam_date: "2026-02-02" },
];

export const mockDocuments: StudentDocument[] = [
  { id: 1, student_id: 1, name: "Aadhaar Card", type: "aadhaar", file_url: "#", file_size: "245 KB", uploaded_by: "Admin", uploaded_at: "2024-01-10" },
  { id: 2, student_id: 1, name: "Birth Certificate", type: "birth_certificate", file_url: "#", file_size: "320 KB", uploaded_by: "Admin", uploaded_at: "2024-01-10" },
  { id: 3, student_id: 1, name: "Passport Photo", type: "photo", file_url: "#", file_size: "150 KB", uploaded_by: "Admin", uploaded_at: "2024-01-10" },
];

export const mockMessages: StudentMessage[] = [
  { id: 1, student_id: 1, template: "Absentee Alert", message: "Dear Parent, your ward Rahul Sharma was absent on 05-Feb-2026.", sent_by: "Mrs. Sharma", sent_at: "2026-02-05 10:30", channel: "whatsapp" },
  { id: 2, student_id: 1, template: "Exam Information", message: "Dear Parent, upcoming exams for Class 10 commence from 01-Mar-2026.", sent_by: "Admin", sent_at: "2026-01-20 09:00", channel: "whatsapp" },
];
