import { TeacherRecord, TeacherDocument, TeacherMessage } from "@/types/teacher";

export const mockTeachers: TeacherRecord[] = [
  {
    id: 1, employee_id: "EMP-001", photo_url: "", full_name: "Priya Singh", gender: "female", dob: "1985-03-14",
    phone: "9876543210", whatsapp_number: "9876543210", email: "priya.singh@school.com",
    address: "12 MG Road, New Delhi", qualification: "M.Sc + B.Ed", experience_years: 12,
    joining_date: "2014-06-01", subjects: ["Mathematics", "Physics"], assigned_classes: ["10-A", "10-B", "9-A"],
    employment_type: "full-time", status: "active", created_at: "2024-01-15",
  },
  {
    id: 2, employee_id: "EMP-002", photo_url: "", full_name: "Rajesh Kumar", gender: "male", dob: "1980-07-22",
    phone: "9876543211", whatsapp_number: "9876543211", email: "rajesh.kumar@school.com",
    address: "45 Park Avenue, Mumbai", qualification: "M.Sc + B.Ed", experience_years: 18,
    joining_date: "2008-04-15", subjects: ["Science", "Biology"], assigned_classes: ["8-A", "8-B", "7-A"],
    employment_type: "full-time", status: "active", created_at: "2024-02-10",
  },
  {
    id: 3, employee_id: "EMP-003", photo_url: "", full_name: "Anita Desai", gender: "female", dob: "1990-11-05",
    phone: "9876543212", whatsapp_number: "9876543212", email: "anita.desai@school.com",
    address: "78 Lake View, Pune", qualification: "M.A + B.Ed", experience_years: 7,
    joining_date: "2019-07-01", subjects: ["English", "Hindi"], assigned_classes: ["11-A", "12-A"],
    employment_type: "full-time", status: "inactive", created_at: "2023-08-20",
  },
  {
    id: 4, employee_id: "EMP-004", photo_url: "", full_name: "Suresh Reddy", gender: "male", dob: "1978-01-30",
    phone: "9876543213", whatsapp_number: "9876543213", email: "suresh.reddy@school.com",
    address: "23 Tech Park, Hyderabad", qualification: "B.Sc + B.Ed", experience_years: 22,
    joining_date: "2004-03-10", subjects: ["Social Science"], assigned_classes: ["6-A", "6-B", "7-B"],
    employment_type: "full-time", status: "active", created_at: "2023-06-01",
  },
  {
    id: 5, employee_id: "EMP-005", photo_url: "", full_name: "Meera Iyer", gender: "female", dob: "1992-06-18",
    phone: "9876543214", whatsapp_number: "9876543214", email: "meera.iyer@school.com",
    address: "56 Green Lane, Chennai", qualification: "M.Sc + B.Ed", experience_years: 5,
    joining_date: "2021-08-01", subjects: ["Computer Science", "Mathematics"], assigned_classes: ["9-B", "11-B"],
    employment_type: "full-time", status: "active", created_at: "2024-03-15",
  },
  {
    id: 6, employee_id: "EMP-006", photo_url: "", full_name: "Arun Mehta", gender: "male", dob: "1988-09-12",
    phone: "9876543215", whatsapp_number: "9876543215", email: "arun.mehta@school.com",
    address: "89 Ring Road, Jaipur", qualification: "B.Ed", experience_years: 10,
    joining_date: "2016-04-01", subjects: ["Physical Education"], assigned_classes: ["10-A", "10-B", "11-A", "12-A"],
    employment_type: "full-time", status: "active", created_at: "2024-01-20",
  },
  {
    id: 7, employee_id: "EMP-007", photo_url: "", full_name: "Kavita Sharma", gender: "female", dob: "1995-04-25",
    phone: "9876543216", whatsapp_number: "9876543216", email: "kavita.sharma@school.com",
    address: "34 Civil Lines, Lucknow", qualification: "M.A + B.Ed", experience_years: 3,
    joining_date: "2023-07-01", subjects: ["Sanskrit", "Hindi"], assigned_classes: ["6-A", "7-A", "8-A"],
    employment_type: "part-time", status: "active", created_at: "2024-04-01",
  },
  {
    id: 8, employee_id: "EMP-008", photo_url: "", full_name: "Deepak Verma", gender: "male", dob: "1975-12-08",
    phone: "9876543217", whatsapp_number: "9876543217", email: "deepak.verma@school.com",
    address: "67 University Road, Chandigarh", qualification: "Ph.D", experience_years: 25,
    joining_date: "2001-01-15", subjects: ["Chemistry", "Physics"], assigned_classes: ["12-A", "12-B"],
    employment_type: "full-time", status: "inactive", created_at: "2022-05-10",
  },
  {
    id: 9, employee_id: "EMP-009", photo_url: "", full_name: "Nisha Patel", gender: "female", dob: "1993-02-14",
    phone: "9876543218", whatsapp_number: "9876543218", email: "nisha.patel@school.com",
    address: "12 Ashram Road, Ahmedabad", qualification: "M.A + B.Ed", experience_years: 6,
    joining_date: "2020-06-15", subjects: ["Economics", "Commerce"], assigned_classes: ["11-A", "11-B", "12-B"],
    employment_type: "full-time", status: "active", created_at: "2024-02-25",
  },
  {
    id: 10, employee_id: "EMP-010", photo_url: "", full_name: "Ravi Shankar", gender: "male", dob: "1987-08-30",
    phone: "9876543219", whatsapp_number: "9876543219", email: "ravi.shankar@school.com",
    address: "45 Station Road, Bhopal", qualification: "B.Ed", experience_years: 8,
    joining_date: "2018-04-01", subjects: ["Art & Craft", "Music"], assigned_classes: ["6-B", "7-B", "8-B", "9-A"],
    employment_type: "part-time", status: "active", created_at: "2024-03-01",
  },
];

export const mockTeacherDocuments: TeacherDocument[] = [
  { id: 1, teacher_id: 1, name: "Aadhaar Card", type: "id_proof", file_url: "#", file_size: "245 KB", uploaded_by: "Admin", uploaded_at: "2024-01-15" },
  { id: 2, teacher_id: 1, name: "M.Sc Degree Certificate", type: "certificate", file_url: "#", file_size: "520 KB", uploaded_by: "Admin", uploaded_at: "2024-01-15" },
  { id: 3, teacher_id: 1, name: "B.Ed Certificate", type: "certificate", file_url: "#", file_size: "480 KB", uploaded_by: "Admin", uploaded_at: "2024-01-15" },
  { id: 4, teacher_id: 1, name: "Resume", type: "resume", file_url: "#", file_size: "350 KB", uploaded_by: "Self", uploaded_at: "2024-01-10" },
  { id: 5, teacher_id: 1, name: "Appointment Letter", type: "appointment_letter", file_url: "#", file_size: "180 KB", uploaded_by: "Admin", uploaded_at: "2014-06-01" },
];

export const mockTeacherMessages: TeacherMessage[] = [
  { id: 1, teacher_id: 1, template: "Meeting Notice", message: "Dear Priya Singh, you are requested to attend a staff meeting on 05-Feb-2026 at 10:00 AM.", sent_by: "Admin", sent_at: "2026-02-04 09:00", channel: "whatsapp" },
  { id: 2, teacher_id: 1, template: "Timetable Update", message: "Dear Priya Singh, please note that your timetable has been updated for the current week.", sent_by: "Admin", sent_at: "2026-01-27 14:30", channel: "whatsapp" },
  { id: 3, teacher_id: 1, template: "Circular", message: "Dear Priya Singh, a new circular has been issued regarding Annual Day preparations.", sent_by: "Admin", sent_at: "2026-01-20 11:00", channel: "whatsapp" },
];
