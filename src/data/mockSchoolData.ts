// ============================================
// JSchoolAdmin — Centralized School Data
// This data powers the public website pages
// In production, this comes from /api/public/* endpoints
// ============================================

export const schoolInfo = {
  name: "JNV Model School",
  shortName: "JNVMS",
  tagline: "Excellence in Education, Character in Action",
  established: 2005,
  affiliation: "CBSE",
  affiliationNo: "2131234",
  medium: "English / Hindi",
  board: "Central Board of Secondary Education",
  address: "123, Education Lane, Lucknow, Uttar Pradesh 226001",
  phone: "+91-522-2345678",
  altPhone: "+91-522-2345679",
  email: "info@jnvmodelschool.edu.in",
  website: "www.jnvmodelschool.edu.in",
  mapUrl: "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3559.9!2d80.9462!3d26.8467!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjbCsDUwJzQ4LjEiTiA4MMKwNTYnNDYuMyJF!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin",
  officeHours: {
    weekdays: "8:00 AM – 3:00 PM",
    saturday: "8:00 AM – 12:00 PM",
    sunday: "Closed",
  },
};

export const principalInfo = {
  name: "Dr. R.K. Tripathi",
  title: "Principal",
  qualification: "Ph.D. in Education, M.Ed., B.Sc.",
  photo: "",
  message:
    "Welcome to JNV Model School, where we believe every child has the potential to shine. Our mission is to provide a holistic learning environment that nurtures intellectual curiosity, builds character, and prepares students for the challenges of tomorrow. With dedicated faculty, modern facilities, and a values-driven curriculum, we are committed to transforming young minds into responsible citizens and future leaders. I invite you to explore our school and join our community of learners.",
};

export const visionMission = {
  vision:
    "To be a centre of excellence that empowers students with knowledge, skills, and values to become responsible global citizens and lifelong learners.",
  mission:
    "To provide quality education through innovative teaching methodologies, holistic development programs, and a nurturing environment that brings out the best in every child.",
  values: [
    { title: "Excellence", description: "Striving for the highest standards in academics and character." },
    { title: "Integrity", description: "Building trust through honesty, transparency, and ethical behavior." },
    { title: "Innovation", description: "Embracing creativity and modern approaches to education." },
    { title: "Respect", description: "Fostering mutual respect, inclusivity, and cultural sensitivity." },
    { title: "Responsibility", description: "Developing a sense of duty towards self, community, and nation." },
    { title: "Compassion", description: "Encouraging empathy and kindness in all interactions." },
  ],
};

export const schoolStats = [
  { label: "Students", value: 1250, suffix: "+" },
  { label: "Teachers", value: 65, suffix: "+" },
  { label: "Years of Excellence", value: new Date().getFullYear() - 2005, suffix: "" },
  { label: "Pass Rate", value: 99, suffix: "%" },
];

export const facilities = [
  { title: "Smart Classrooms", description: "Digital boards and projectors in every classroom for interactive learning.", icon: "Monitor" },
  { title: "Science Laboratories", description: "Fully equipped Physics, Chemistry, and Biology labs with modern instruments.", icon: "FlaskConical" },
  { title: "Computer Lab", description: "50+ computers with high-speed internet and latest software for IT education.", icon: "Laptop" },
  { title: "Library", description: "10,000+ books, journals, and digital resources for students and staff.", icon: "BookOpen" },
  { title: "Sports Complex", description: "Cricket ground, basketball court, athletics track, and indoor games facility.", icon: "Trophy" },
  { title: "Transport", description: "GPS-enabled school buses covering all major routes across the city.", icon: "Bus" },
  { title: "Medical Room", description: "On-campus medical room with a trained nurse and first-aid facilities.", icon: "HeartPulse" },
  { title: "Auditorium", description: "500-seat air-conditioned auditorium for events, seminars, and performances.", icon: "Theater" },
];

export const achievements = [
  { year: "2025", title: "Best CBSE School Award", description: "Recognized by the District Education Board for academic excellence." },
  { year: "2024", title: "National Science Olympiad Winners", description: "3 students qualified for the national-level science olympiad finals." },
  { year: "2024", title: "100% Board Results", description: "All Class 10 and 12 students passed with distinction." },
  { year: "2023", title: "State-Level Sports Champions", description: "Inter-school athletics championship winners for the 3rd consecutive year." },
  { year: "2023", title: "Green Campus Award", description: "Awarded for eco-friendly campus practices and waste management initiatives." },
  { year: "2022", title: "Digital Innovation Award", description: "Recognized for early adoption of smart classroom technology across all grades." },
];

export const academicInfo = {
  classesOffered: [
    { group: "Pre-Primary", classes: ["Nursery", "LKG", "UKG"], curriculum: "Play-based Learning" },
    { group: "Primary (1–5)", classes: ["Class 1", "Class 2", "Class 3", "Class 4", "Class 5"], curriculum: "CBSE (NCF 2023)" },
    { group: "Middle (6–8)", classes: ["Class 6", "Class 7", "Class 8"], curriculum: "CBSE" },
    { group: "Secondary (9–10)", classes: ["Class 9", "Class 10"], curriculum: "CBSE Board" },
    { group: "Senior Secondary (11–12)", classes: ["Class 11", "Class 12"], curriculum: "CBSE Board", streams: ["Science (PCM/PCB)", "Commerce", "Humanities"] },
  ],
  subjects: {
    primary: ["English", "Hindi", "Mathematics", "EVS", "Computer Science", "Art & Craft", "Physical Education"],
    middle: ["English", "Hindi", "Sanskrit", "Mathematics", "Science", "Social Science", "Computer Science", "Physical Education", "Art"],
    secondary: ["English", "Hindi", "Mathematics", "Science", "Social Science", "Computer Applications", "Physical Education"],
    seniorScience: ["Physics", "Chemistry", "Mathematics/Biology", "English", "Computer Science/Physical Education"],
    seniorCommerce: ["Accountancy", "Business Studies", "Economics", "English", "Mathematics/Informatics Practices"],
    seniorHumanities: ["History", "Political Science", "Geography/Psychology", "English", "Economics"],
  },
  examPattern: [
    { name: "Unit Test 1", month: "July", weightage: "10%" },
    { name: "Mid-Term Exam", month: "September", weightage: "30%" },
    { name: "Unit Test 2", month: "November", weightage: "10%" },
    { name: "Final Exam", month: "February–March", weightage: "50%" },
  ],
  activities: [
    "Science Club & Robotics",
    "Debate & Elocution",
    "Music & Dance",
    "Art & Painting",
    "National Cadet Corps (NCC)",
    "Scouts & Guides",
    "Yoga & Meditation",
    "Annual Sports Meet",
    "Inter-House Competitions",
    "Field Trips & Excursions",
  ],
};

export const mockPublicNotifications = [
  { id: 1, title: "Annual Day Celebration 2026", body: "Annual Day will be celebrated on 15th March 2026. All parents and guardians are cordially invited to attend the cultural programme and prize distribution ceremony at the School Auditorium.", urgency: "important" as const, expiry: "2026-03-15", attachment_url: "/notice.pdf", created_at: "2026-02-01" },
  { id: 2, title: "Parent-Teacher Meeting Schedule", body: "Quarterly Parent-Teacher Meeting is scheduled for 20th February 2026 from 10:00 AM to 1:00 PM. Parents are requested to attend and discuss their ward's academic progress with class teachers.", urgency: "important" as const, expiry: "2026-02-20", created_at: "2026-02-05" },
  { id: 3, title: "Republic Day Celebration", body: "Republic Day will be celebrated on 26th January 2026 with flag hoisting ceremony, patriotic song competition, and cultural performances. All students must report in full uniform by 7:30 AM.", urgency: "normal" as const, expiry: "2026-01-26", created_at: "2026-01-20" },
  { id: 4, title: "Admission Open for 2026-27", body: "Admissions are open for classes Nursery to Class 9 for the academic session 2026-27. Visit the school office or apply online through the Admissions page. Limited seats available.", urgency: "important" as const, expiry: "2026-04-30", attachment_url: "/admission-brochure.pdf", created_at: "2026-01-15" },
  { id: 5, title: "Winter Uniform Notice", body: "All students must wear complete winter uniform from November onwards. Students without proper uniform will not be allowed to attend classes. Uniform is available at the school store.", urgency: "normal" as const, expiry: "2026-02-28", created_at: "2025-11-01" },
  { id: 6, title: "Holi Holiday Notice", body: "School will remain closed on 10th March 2026 (Tuesday) on account of Holi festival. Regular classes resume on 11th March 2026.", urgency: "normal" as const, expiry: "2026-03-11", created_at: "2026-03-05" },
  { id: 7, title: "Science Exhibition – Entries Open", body: "Annual Science Exhibition will be held on 28th February 2026. Students of classes 6 to 12 can submit their projects by 20th February. Best projects will be awarded prizes.", urgency: "normal" as const, expiry: "2026-02-28", created_at: "2026-02-01" },
  { id: 8, title: "Bus Route Changes from February", body: "Due to road construction work near Gomti Nagar, Bus Route #3 and #5 will follow alternate routes from 1st February. Updated route maps are available at the office.", urgency: "urgent" as const, expiry: "2026-03-31", created_at: "2026-01-28" },
];

export const mockPublicEvents = [
  { id: 1, title: "Republic Day Celebration", description: "Flag hoisting ceremony followed by patriotic cultural programme, NCC march past, and speech competition.", date: "2026-01-26", time: "08:00 AM", location: "School Ground", type: "cultural" },
  { id: 2, title: "Science Exhibition", description: "Annual science exhibition showcasing student projects from classes 6 to 12. Guest judges from IIT Lucknow.", date: "2026-02-28", time: "09:00 AM", location: "School Auditorium", type: "academic" },
  { id: 3, title: "Parent-Teacher Meeting", description: "Quarterly PTM for all classes. Parents can discuss academic progress and areas of improvement with respective class teachers.", date: "2026-02-20", time: "10:00 AM", location: "Respective Classrooms", type: "academic" },
  { id: 4, title: "Inter-House Debate Competition", description: "Annual inter-house debate competition on 'Role of AI in Education'. Open to classes 9 to 12.", date: "2026-02-15", time: "11:00 AM", location: "School Auditorium", type: "cultural" },
  { id: 5, title: "Holi Holiday", description: "School will remain closed on account of Holi festival. Classes resume on 11th March 2026.", date: "2026-03-10", type: "holiday" },
  { id: 6, title: "Annual Day Celebration", description: "Grand annual day celebration featuring cultural programmes, dance performances, drama, prize distribution, and chief guest address.", date: "2026-03-15", time: "10:00 AM", location: "School Auditorium", type: "cultural" },
  { id: 7, title: "Mid-Term Examinations", description: "Mid-term examinations for classes 6 to 12. Detailed schedule and syllabus available on the notice board.", date: "2026-03-20", time: "09:00 AM", location: "Examination Hall", type: "academic" },
  { id: 8, title: "Annual Sports Day", description: "Annual athletics competition with track and field events, tug of war, relay races, and inter-house matches.", date: "2026-04-10", time: "08:30 AM", location: "Sports Ground", type: "sports" },
  { id: 9, title: "Summer Vacation Begins", description: "Summer vacation starts from 15th May 2026. New session for 2026-27 begins on 1st July 2026.", date: "2026-05-15", type: "holiday" },
  { id: 10, title: "Yoga Day Celebration", description: "International Yoga Day celebration with mass yoga session for students and staff. Guest instructor from Patanjali Yogpeeth.", date: "2026-06-21", time: "07:00 AM", location: "School Ground", type: "sports" },
];

export const galleryCategories = [
  {
    id: 1, name: "Annual Day 2025", slug: "annual-day-2025", type: "images" as const,
    items: [
      { id: 1, url: "https://images.unsplash.com/photo-1523050854058-8df90110c7f1?w=400", title: "Stage Decoration" },
      { id: 2, url: "https://images.unsplash.com/photo-1577896851231-70ef18881754?w=400", title: "Student Performance" },
      { id: 3, url: "https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400", title: "Group Photo" },
      { id: 4, url: "https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=400", title: "Award Ceremony" },
      { id: 11, url: "https://images.unsplash.com/photo-1588072432836-e10032774350?w=400", title: "Dance Performance" },
      { id: 12, url: "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=400", title: "Chief Guest Address" },
    ],
  },
  {
    id: 2, name: "Sports Day", slug: "sports-day", type: "images" as const,
    items: [
      { id: 5, url: "https://images.unsplash.com/photo-1461896836934-bd45ba8fcf9b?w=400", title: "100m Sprint" },
      { id: 6, url: "https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=400", title: "Long Jump" },
      { id: 13, url: "https://images.unsplash.com/photo-1517649763962-0c623066013b?w=400", title: "Relay Race" },
      { id: 14, url: "https://images.unsplash.com/photo-1574629810360-7efbbe195018?w=400", title: "Football Match" },
    ],
  },
  {
    id: 3, name: "Classroom Activities", slug: "classroom-activities", type: "images" as const,
    items: [
      { id: 15, url: "https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=400", title: "Science Lab" },
      { id: 16, url: "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400", title: "Library Session" },
      { id: 17, url: "https://images.unsplash.com/photo-1427504494785-3a9ca7044f45?w=400", title: "Computer Lab" },
      { id: 18, url: "https://images.unsplash.com/photo-1596495578065-6e0763fa1178?w=400", title: "Art Class" },
    ],
  },
  {
    id: 4, name: "Republic Day 2026", slug: "republic-day-2026", type: "images" as const,
    items: [
      { id: 19, url: "https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=400", title: "Flag Hoisting" },
      { id: 20, url: "https://images.unsplash.com/photo-1606761568499-6d2451b23c66?w=400", title: "March Past" },
      { id: 21, url: "https://images.unsplash.com/photo-1540479859555-17af45c78602?w=400", title: "Patriotic Dance" },
    ],
  },
  {
    id: 5, name: "School Campus", slug: "campus", type: "images" as const,
    items: [
      { id: 22, url: "https://images.unsplash.com/photo-1580537659466-0a9bfa916a54?w=400", title: "Main Building" },
      { id: 23, url: "https://images.unsplash.com/photo-1562774053-701939374585?w=400", title: "School Entrance" },
      { id: 24, url: "https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?w=400", title: "Playground" },
    ],
  },
  {
    id: 6, name: "School Videos", slug: "videos", type: "videos" as const,
    items: [
      { id: 7, url: "dQw4w9WgXcQ", title: "Annual Day Highlights 2025" },
      { id: 8, url: "dQw4w9WgXcQ", title: "Sports Day Montage" },
      { id: 9, url: "dQw4w9WgXcQ", title: "Virtual Campus Tour" },
    ],
  },
];
