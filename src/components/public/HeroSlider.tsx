import { useState, useEffect, useCallback, useRef } from "react";
import { Link } from "react-router-dom";
import { ArrowRight, ChevronLeft, ChevronRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { cn } from "@/lib/utils";
import { SliderSlide } from "@/data/mockSliderData";
import { Skeleton } from "@/components/ui/skeleton";

interface HeroSliderProps {
  slides: SliderSlide[];
  loading?: boolean;
}

export function HeroSlider({ slides, loading }: HeroSliderProps) {
  const [current, setCurrent] = useState(0);
  const [direction, setDirection] = useState<"next" | "prev">("next");
  const [isHovered, setIsHovered] = useState(false);
  const touchStartX = useRef(0);
  const touchEndX = useRef(0);
  const timerRef = useRef<ReturnType<typeof setInterval>>();

  const activeSlides = slides.filter((s) => s.is_active).sort((a, b) => a.sort_order - b.sort_order);

  const goTo = useCallback(
    (index: number, dir?: "next" | "prev") => {
      if (activeSlides.length === 0) return;
      setDirection(dir ?? (index > current ? "next" : "prev"));
      setCurrent((index + activeSlides.length) % activeSlides.length);
    },
    [activeSlides.length, current]
  );

  const next = useCallback(() => goTo(current + 1, "next"), [current, goTo]);
  const prev = useCallback(() => goTo(current - 1, "prev"), [current, goTo]);

  // Auto-slide every 5s, pause on hover
  useEffect(() => {
    if (isHovered || activeSlides.length <= 1) return;
    timerRef.current = setInterval(next, 5000);
    return () => clearInterval(timerRef.current);
  }, [next, isHovered, activeSlides.length]);

  // Touch handlers
  const onTouchStart = (e: React.TouchEvent) => {
    touchStartX.current = e.changedTouches[0].screenX;
  };
  const onTouchEnd = (e: React.TouchEvent) => {
    touchEndX.current = e.changedTouches[0].screenX;
    const diff = touchStartX.current - touchEndX.current;
    if (Math.abs(diff) > 50) {
      diff > 0 ? next() : prev();
    }
  };

  // Preload images
  useEffect(() => {
    activeSlides.forEach((s) => {
      const img = new Image();
      img.src = s.image_url;
    });
  }, [activeSlides]);

  if (loading) {
    return (
      <section className="relative w-full h-[400px] md:h-[550px] lg:h-[600px] bg-muted">
        <Skeleton className="w-full h-full rounded-none" />
      </section>
    );
  }

  if (activeSlides.length === 0) return null;

  const slide = activeSlides[current];

  return (
    <section
      className="relative w-full h-[400px] md:h-[550px] lg:h-[600px] overflow-hidden"
      onMouseEnter={() => setIsHovered(true)}
      onMouseLeave={() => setIsHovered(false)}
      onTouchStart={onTouchStart}
      onTouchEnd={onTouchEnd}
    >
      {/* Slides */}
      {activeSlides.map((s, i) => (
        <div
          key={s.id}
          className={cn(
            "absolute inset-0 transition-all duration-700 ease-in-out",
            i === current
              ? "opacity-100 scale-100 z-10"
              : "opacity-0 scale-105 z-0"
          )}
        >
          {/* BG image */}
          <img
            src={s.image_url}
            alt={s.title.replace("\n", " ")}
            className="absolute inset-0 w-full h-full object-cover"
            loading={i === 0 ? "eager" : "lazy"}
          />
          {/* Overlay */}
          <div className="absolute inset-0 bg-gradient-to-br from-primary/85 via-primary/75 to-primary/65" />
          {/* Pattern */}
          <div className="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmZmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0djJIMjR2LTJoMTJ6TTM2IDI0djJIMjR2LTJoMTJ6Ii8+PC9nPjwvZz48L3N2Zz4=')] opacity-30" />
        </div>
      ))}

      {/* Content */}
      <div className="relative z-20 h-full flex items-center justify-center px-4">
        <div
          key={slide.id}
          className={cn(
            "max-w-5xl mx-auto text-center space-y-5 md:space-y-6 animate-fade-in"
          )}
        >
          <Badge
            variant="secondary"
            className="text-sm px-4 py-1.5 bg-primary-foreground/15 text-primary-foreground border-primary-foreground/20"
          >
            {slide.badge_text}
          </Badge>
          <h1 className="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold tracking-tight leading-tight text-primary-foreground whitespace-pre-line">
            {slide.title}
          </h1>
          <p className="text-base md:text-lg lg:text-xl text-primary-foreground/90 max-w-3xl mx-auto">
            {slide.subtitle}
          </p>
          <div className="flex gap-3 justify-center flex-wrap pt-2">
            <Button size="lg" variant="secondary" asChild className="text-base">
              <Link to={slide.cta_primary_link}>
                {slide.cta_primary_text} <ArrowRight className="ml-2 h-4 w-4" />
              </Link>
            </Button>
            <Button
              size="lg"
              variant="outline"
              className="bg-transparent border-primary-foreground/30 text-primary-foreground hover:bg-primary-foreground/10 text-base"
              asChild
            >
              <Link to={slide.cta_secondary_link}>{slide.cta_secondary_text}</Link>
            </Button>
          </div>
        </div>
      </div>

      {/* Arrows — desktop only */}
      {activeSlides.length > 1 && (
        <>
          <button
            onClick={prev}
            className="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-primary-foreground/15 hover:bg-primary-foreground/25 backdrop-blur-sm items-center justify-center text-primary-foreground transition-colors"
            aria-label="Previous slide"
          >
            <ChevronLeft className="h-5 w-5" />
          </button>
          <button
            onClick={next}
            className="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 z-30 w-10 h-10 rounded-full bg-primary-foreground/15 hover:bg-primary-foreground/25 backdrop-blur-sm items-center justify-center text-primary-foreground transition-colors"
            aria-label="Next slide"
          >
            <ChevronRight className="h-5 w-5" />
          </button>
        </>
      )}

      {/* Dots */}
      {activeSlides.length > 1 && (
        <div className="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex gap-2">
          {activeSlides.map((_, i) => (
            <button
              key={i}
              onClick={() => goTo(i)}
              className={cn(
                "h-2.5 rounded-full transition-all duration-300",
                i === current
                  ? "w-8 bg-primary-foreground"
                  : "w-2.5 bg-primary-foreground/40 hover:bg-primary-foreground/60"
              )}
              aria-label={`Go to slide ${i + 1}`}
            />
          ))}
        </div>
      )}
    </section>
  );
}
