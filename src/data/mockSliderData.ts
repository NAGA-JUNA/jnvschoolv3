// ============================================
// Home Page Slider Data
// In production, this comes from /api/home/slider
// ============================================

export interface SliderSlide {
  id: number;
  title: string;
  subtitle: string;
  badge_text: string;
  cta_primary_text: string;
  cta_primary_link: string;
  cta_secondary_text: string;
  cta_secondary_link: string;
  image_url: string;
  is_active: boolean;
  sort_order: number;
}

export const mockSliderSlides: SliderSlide[] = [
  {
    id: 1,
    title: "Welcome to\nJNV Model School",
    subtitle: "Excellence in Education, Character in Action",
    badge_text: "CBSE Affiliated • Est. 2005",
    cta_primary_text: "Apply Now",
    cta_primary_link: "/admissions",
    cta_secondary_text: "Learn More",
    cta_secondary_link: "/about",
    image_url: "https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=1920&q=80",
    is_active: true,
    sort_order: 1,
  },
  {
    id: 2,
    title: "Admissions Open\n2026-27",
    subtitle: "Limited seats available for Nursery to Class 9. Join our family of learners!",
    badge_text: "Enroll Now • Limited Seats",
    cta_primary_text: "Apply Now",
    cta_primary_link: "/admissions",
    cta_secondary_text: "Contact Us",
    cta_secondary_link: "/contact",
    image_url: "https://images.unsplash.com/photo-1562774053-701939374585?w=1920&q=80",
    is_active: true,
    sort_order: 2,
  },
  {
    id: 3,
    title: "State-of-the-Art\nFacilities",
    subtitle: "Smart classrooms, modern labs, sports complex, and a 500-seat auditorium",
    badge_text: "Campus Tour Available",
    cta_primary_text: "View Gallery",
    cta_primary_link: "/gallery",
    cta_secondary_text: "Our Academics",
    cta_secondary_link: "/academics",
    image_url: "https://images.unsplash.com/photo-1497633762265-9d179a990aa6?w=1920&q=80",
    is_active: true,
    sort_order: 3,
  },
  {
    id: 4,
    title: "Experienced &\nDedicated Faculty",
    subtitle: "65+ qualified teachers committed to nurturing every child's potential",
    badge_text: "Meet Our Team",
    cta_primary_text: "Our Faculty",
    cta_primary_link: "/faculty",
    cta_secondary_text: "About Us",
    cta_secondary_link: "/about",
    image_url: "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1920&q=80",
    is_active: true,
    sort_order: 4,
  },
];
