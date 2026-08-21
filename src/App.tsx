import { useEffect, useRef, useState } from "react";
import logoImg from "@/imports/logo.png";

/* ─── Modal ───────────────────────────────────────── */
type ModalProps = {
  open: boolean;
  onClose: () => void;
  room?: string;
};

function BookingModal({ open, onClose, room }: ModalProps) {
  const [form, setForm] = useState({ name: "", phone: "", email: "", checkin: "", checkout: "", guests: "1", comment: "" });
  const [sent, setSent] = useState(false);
  const overlayRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (open) {
      document.body.style.overflow = "hidden";
      setSent(false);
    } else {
      document.body.style.overflow = "";
    }
    return () => { document.body.style.overflow = ""; };
  }, [open]);

  useEffect(() => {
    const fn = (e: KeyboardEvent) => { if (e.key === "Escape") onClose(); };
    window.addEventListener("keydown", fn);
    return () => window.removeEventListener("keydown", fn);
  }, [onClose]);

  if (!open) return null;

  const set = (k: keyof typeof form) => (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) =>
    setForm((p) => ({ ...p, [k]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setSent(true);
  };

  const inputStyle: React.CSSProperties = {
    width: "100%",
    background: "#0e0d0b",
    border: "1px solid rgba(201,169,110,0.2)",
    borderRadius: 2,
    padding: "12px 16px",
    fontFamily: "'Outfit', sans-serif",
    fontSize: 15,
    color: "#f0ebe2",
    outline: "none",
    transition: "border-color 0.2s ease",
  };

  const labelStyle: React.CSSProperties = {
    fontFamily: "'DM Mono', monospace",
    fontSize: 10,
    letterSpacing: "0.14em",
    textTransform: "uppercase" as const,
    color: "#8a7048",
    display: "block",
    marginBottom: 8,
  };

  return (
    <div
      ref={overlayRef}
      onClick={(e) => { if (e.target === overlayRef.current) onClose(); }}
      style={{
        position: "fixed", inset: 0, zIndex: 200,
        background: "rgba(8,7,6,0.85)",
        backdropFilter: "blur(6px)",
        display: "flex", alignItems: "center", justifyContent: "center",
        padding: "20px",
        animation: "fadeSlideUp 0.3s ease forwards",
      }}
    >
      <div style={{
        background: "#111009",
        border: "1px solid rgba(201,169,110,0.18)",
        borderRadius: 4,
        width: "100%",
        maxWidth: 560,
        maxHeight: "90vh",
        overflowY: "auto",
        position: "relative",
      }}>
        {/* Header */}
        <div style={{ padding: "32px 36px 28px", borderBottom: "1px solid rgba(201,169,110,0.12)", display: "flex", alignItems: "center", justifyContent: "space-between" }}>
          <div style={{ display: "flex", alignItems: "center", gap: 16 }}>
            <img src={logoImg} alt="Troya Hotel" style={{ height: 48, objectFit: "contain" }} />
            <div>
              <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 10, letterSpacing: "0.18em", color: "#c9a96e", textTransform: "uppercase" }}>Онлайн-бронирование</p>
              {room && <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 14, color: "#b8b0a4", marginTop: 4 }}>{room}</p>}
            </div>
          </div>
          <button
            onClick={onClose}
            style={{ background: "none", border: "1px solid rgba(201,169,110,0.2)", borderRadius: 2, color: "#8a7048", cursor: "pointer", width: 36, height: 36, fontSize: 18, display: "flex", alignItems: "center", justifyContent: "center", transition: "all 0.2s ease" }}
            onMouseEnter={(e) => { (e.currentTarget as HTMLButtonElement).style.borderColor = "#c9a96e"; (e.currentTarget as HTMLButtonElement).style.color = "#c9a96e"; }}
            onMouseLeave={(e) => { (e.currentTarget as HTMLButtonElement).style.borderColor = "rgba(201,169,110,0.2)"; (e.currentTarget as HTMLButtonElement).style.color = "#8a7048"; }}
          >
            ✕
          </button>
        </div>

        {sent ? (
          <div style={{ padding: "56px 36px", textAlign: "center" }}>
            <div style={{ fontSize: 40, marginBottom: 20 }}>✓</div>
            <h3 style={{ fontFamily: "'Fraunces', serif", fontSize: 28, fontWeight: 300, color: "#f0ebe2", marginBottom: 12 }}>Заявка отправлена</h3>
            <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 15, color: "#b8b0a4", lineHeight: 1.7, marginBottom: 32 }}>
              Мы свяжемся с вами в ближайшее время для подтверждения бронирования.<br />
              <strong style={{ color: "#f0ebe2" }}>8 (843) 564-46-46</strong>
            </p>
            <button
              onClick={onClose}
              style={{ fontFamily: "'Outfit', sans-serif", fontSize: 14, fontWeight: 600, color: "#0c0b09", background: "#c9a96e", border: "none", padding: "12px 32px", borderRadius: 2, cursor: "pointer", letterSpacing: "0.06em" }}
            >
              Закрыть
            </button>
          </div>
        ) : (
          <form onSubmit={handleSubmit} style={{ padding: "32px 36px" }}>
            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 20, marginBottom: 20 }}>
              <div>
                <label style={labelStyle}>Ваше имя *</label>
                <input required value={form.name} onChange={set("name")} placeholder="Иван Иванов" style={inputStyle}
                  onFocus={(e) => (e.currentTarget.style.borderColor = "#c9a96e")}
                  onBlur={(e) => (e.currentTarget.style.borderColor = "rgba(201,169,110,0.2)")} />
              </div>
              <div>
                <label style={labelStyle}>Телефон *</label>
                <input required type="tel" value={form.phone} onChange={set("phone")} placeholder="+7 (___) ___-__-__" style={inputStyle}
                  onFocus={(e) => (e.currentTarget.style.borderColor = "#c9a96e")}
                  onBlur={(e) => (e.currentTarget.style.borderColor = "rgba(201,169,110,0.2)")} />
              </div>
            </div>

            <div style={{ marginBottom: 20 }}>
              <label style={labelStyle}>Email</label>
              <input type="email" value={form.email} onChange={set("email")} placeholder="your@email.com" style={inputStyle}
                onFocus={(e) => (e.currentTarget.style.borderColor = "#c9a96e")}
                onBlur={(e) => (e.currentTarget.style.borderColor = "rgba(201,169,110,0.2)")} />
            </div>

            <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr 1fr", gap: 16, marginBottom: 20 }}>
              <div>
                <label style={labelStyle}>Заезд *</label>
                <input required type="date" value={form.checkin} onChange={set("checkin")} style={{ ...inputStyle, colorScheme: "dark" }}
                  onFocus={(e) => (e.currentTarget.style.borderColor = "#c9a96e")}
                  onBlur={(e) => (e.currentTarget.style.borderColor = "rgba(201,169,110,0.2)")} />
              </div>
              <div>
                <label style={labelStyle}>Выезд *</label>
                <input required type="date" value={form.checkout} onChange={set("checkout")} style={{ ...inputStyle, colorScheme: "dark" }}
                  onFocus={(e) => (e.currentTarget.style.borderColor = "#c9a96e")}
                  onBlur={(e) => (e.currentTarget.style.borderColor = "rgba(201,169,110,0.2)")} />
              </div>
              <div>
                <label style={labelStyle}>Гостей</label>
                <select value={form.guests} onChange={set("guests")} style={{ ...inputStyle, cursor: "pointer" }}
                  onFocus={(e) => (e.currentTarget.style.borderColor = "#c9a96e")}
                  onBlur={(e) => (e.currentTarget.style.borderColor = "rgba(201,169,110,0.2)")}>
                  {["1","2","3","4"].map(n => <option key={n} value={n} style={{ background: "#111009" }}>{n} {n === "1" ? "гость" : n === "4" ? "гостя" : "гостя"}</option>)}
                </select>
              </div>
            </div>

            {room && (
              <div style={{ marginBottom: 20 }}>
                <label style={labelStyle}>Тип номера</label>
                <input readOnly value={room} style={{ ...inputStyle, color: "#c9a96e", cursor: "default" }} />
              </div>
            )}

            <div style={{ marginBottom: 28 }}>
              <label style={labelStyle}>Комментарий</label>
              <textarea value={form.comment} onChange={set("comment")} rows={3} placeholder="Особые пожелания, ранний заезд..." style={{ ...inputStyle, resize: "none" }}
                onFocus={(e) => (e.currentTarget.style.borderColor = "#c9a96e")}
                onBlur={(e) => (e.currentTarget.style.borderColor = "rgba(201,169,110,0.2)")} />
            </div>

            <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 10, color: "#8a7048", letterSpacing: "0.1em", marginBottom: 20, lineHeight: 1.6 }}>
              * ОБЯЗАТЕЛЬНЫЕ ПОЛЯ · ЗАВТРАК +300 ₽ · ДОП. КРОВАТЬ +1 200 ₽
            </p>

            <button
              type="submit"
              style={{ width: "100%", fontFamily: "'Outfit', sans-serif", fontSize: 15, fontWeight: 600, color: "#0c0b09", background: "#c9a96e", border: "none", padding: "16px", borderRadius: 2, cursor: "pointer", letterSpacing: "0.06em", transition: "background 0.2s ease" }}
              onMouseEnter={(e) => ((e.currentTarget as HTMLButtonElement).style.background = "#e2c99a")}
              onMouseLeave={(e) => ((e.currentTarget as HTMLButtonElement).style.background = "#c9a96e")}
            >
              Отправить заявку
            </button>
          </form>
        )}
      </div>
    </div>
  );
}

/* ─── Scroll reveal hook ─────────────────────────── */
function useReveal() {
  useEffect(() => {
    const els = document.querySelectorAll(".reveal, .reveal-left, .reveal-right");
    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((e) => {
          if (e.isIntersecting) {
            e.target.classList.add("visible");
            obs.unobserve(e.target);
          }
        });
      },
      { threshold: 0.12 }
    );
    els.forEach((el) => obs.observe(el));
    return () => obs.disconnect();
  }, []);
}

/* ─── Nav ─────────────────────────────────────────── */
function Nav({ onBook }: { onBook: () => void }) {
  const [scrolled, setScrolled] = useState(false);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const fn = () => setScrolled(window.scrollY > 60);
    window.addEventListener("scroll", fn);
    return () => window.removeEventListener("scroll", fn);
  }, []);

  const links = [
    { label: "Номера", href: "#rooms" },
    { label: "Удобства", href: "#amenities" },
    { label: "Экскурсии", href: "#excursions" },
    { label: "Отзывы", href: "#reviews" },
    { label: "Контакты", href: "#contact" },
  ];

  return (
    <header
      style={{
        position: "fixed",
        top: 0,
        left: 0,
        right: 0,
        zIndex: 100,
        transition: "background 0.4s ease, backdrop-filter 0.4s ease, border-color 0.4s ease",
        background: scrolled ? "rgba(12,11,9,0.92)" : "transparent",
        backdropFilter: scrolled ? "blur(12px)" : "none",
        borderBottom: scrolled ? "1px solid rgba(201,169,110,0.12)" : "1px solid transparent",
      }}
    >
      <div style={{ maxWidth: 1280, margin: "0 auto", padding: "0 32px", height: 72, display: "flex", alignItems: "center", justifyContent: "space-between" }}>
        <a href="#" style={{ textDecoration: "none", display: "flex", alignItems: "center" }}>
          <img src={logoImg} alt="Troya Hotel" style={{ height: scrolled ? 44 : 52, objectFit: "contain", transition: "height 0.3s ease", filter: "brightness(1.05)" }} />
        </a>

        {/* Desktop nav */}
        <nav style={{ display: "flex", gap: 32, alignItems: "center" }}>
          {links.map((l) => (
            <a
              key={l.href}
              href={l.href}
              className="nav-link"
              style={{ fontFamily: "'Outfit', sans-serif", fontSize: 13, fontWeight: 500, color: "#b8b0a4", textDecoration: "none", letterSpacing: "0.08em", textTransform: "uppercase" }}
            >
              {l.label}
            </a>
          ))}
          <button
            onClick={onBook}
            style={{
              fontFamily: "'Outfit', sans-serif",
              fontSize: 13,
              fontWeight: 600,
              color: "#0c0b09",
              background: "#c9a96e",
              padding: "8px 20px",
              borderRadius: 2,
              border: "none",
              cursor: "pointer",
              letterSpacing: "0.05em",
              transition: "background 0.2s ease",
            }}
            onMouseEnter={(e) => ((e.currentTarget as HTMLButtonElement).style.background = "#e2c99a")}
            onMouseLeave={(e) => ((e.currentTarget as HTMLButtonElement).style.background = "#c9a96e")}
          >
            Забронировать
          </button>
        </nav>
      </div>
    </header>
  );
}

/* ─── Hero ────────────────────────────────────────── */
function Hero({ onBook }: { onBook: () => void }) {
  return (
    <section style={{ position: "relative", height: "100vh", minHeight: 600, overflow: "hidden", display: "flex", alignItems: "center" }}>
      {/* Background image */}
      <div style={{ position: "absolute", inset: 0 }}>
        <img
          className="hero-img"
          src="https://images.unsplash.com/photo-1629140727571-9b5c6f6267b4?w=1800&h=1200&fit=crop&auto=format"
          alt="Номер отеля Троя"
          style={{ width: "100%", height: "100%", objectFit: "cover", transformOrigin: "center" }}
        />
        <div
          style={{
            position: "absolute",
            inset: 0,
            background: "linear-gradient(105deg, rgba(12,11,9,0.88) 40%, rgba(12,11,9,0.45) 100%)",
          }}
        />
      </div>

      {/* City strip — decorative vertical text */}
      <div style={{ position: "absolute", right: 48, top: "50%", transform: "translateY(-50%) rotate(90deg)", fontFamily: "'DM Mono', monospace", fontSize: 10, letterSpacing: "0.2em", color: "rgba(201,169,110,0.5)", textTransform: "uppercase" }}>
        Казань — ул. Восстания, 119
      </div>

      <div style={{ position: "relative", maxWidth: 1280, margin: "0 auto", padding: "0 32px", width: "100%" }}>
        <p
          style={{
            fontFamily: "'DM Mono', monospace",
            fontSize: 11,
            letterSpacing: "0.25em",
            color: "#c9a96e",
            textTransform: "uppercase",
            marginBottom: 24,
            opacity: 0,
            animation: "fadeSlideUp 0.8s 0.3s cubic-bezier(0.22,1,0.36,1) forwards",
          }}
        >
          Отель в Казани
        </p>
        <h1
          style={{
            fontFamily: "'Fraunces', serif",
            fontSize: "clamp(52px, 8vw, 108px)",
            fontWeight: 300,
            lineHeight: 1.0,
            color: "#f0ebe2",
            maxWidth: 700,
            marginBottom: 32,
            opacity: 0,
            animation: "fadeSlideUp 0.9s 0.5s cubic-bezier(0.22,1,0.36,1) forwards",
          }}
        >
          Ваше идеальное
          <br />
          <em style={{ color: "#c9a96e", fontStyle: "italic" }}>укрытие</em>
          <br />
          от суеты
        </h1>
        <p
          style={{
            fontFamily: "'Outfit', sans-serif",
            fontSize: 16,
            fontWeight: 300,
            color: "#b8b0a4",
            maxWidth: 420,
            lineHeight: 1.7,
            marginBottom: 48,
            opacity: 0,
            animation: "fadeSlideUp 0.9s 0.7s cubic-bezier(0.22,1,0.36,1) forwards",
          }}
        >
          Комфортабельный отель в самом сердце Казани. Современные номера, завтраки, парковка и организация экскурсий.
        </p>
        <div
          style={{
            display: "flex",
            gap: 16,
            alignItems: "center",
            opacity: 0,
            animation: "fadeSlideUp 0.9s 0.9s cubic-bezier(0.22,1,0.36,1) forwards",
          }}
        >
          <button
            onClick={onBook}
            style={{
              fontFamily: "'Outfit', sans-serif",
              fontSize: 14,
              fontWeight: 600,
              color: "#0c0b09",
              background: "#c9a96e",
              padding: "14px 36px",
              borderRadius: 2,
              border: "none",
              cursor: "pointer",
              letterSpacing: "0.06em",
              transition: "background 0.2s ease, transform 0.2s ease",
            }}
            onMouseEnter={(e) => { (e.currentTarget as HTMLButtonElement).style.background = "#e2c99a"; (e.currentTarget as HTMLButtonElement).style.transform = "translateY(-2px)"; }}
            onMouseLeave={(e) => { (e.currentTarget as HTMLButtonElement).style.background = "#c9a96e"; (e.currentTarget as HTMLButtonElement).style.transform = "none"; }}
          >
            Забронировать номер
          </button>
          <a
            href="#rooms"
            style={{
              fontFamily: "'Outfit', sans-serif",
              fontSize: 14,
              fontWeight: 500,
              color: "#f0ebe2",
              border: "1px solid rgba(240,235,226,0.3)",
              padding: "14px 36px",
              borderRadius: 2,
              textDecoration: "none",
              letterSpacing: "0.06em",
              transition: "border-color 0.2s ease, color 0.2s ease",
            }}
            onMouseEnter={(e) => { e.currentTarget.style.borderColor = "#c9a96e"; e.currentTarget.style.color = "#c9a96e"; }}
            onMouseLeave={(e) => { e.currentTarget.style.borderColor = "rgba(240,235,226,0.3)"; e.currentTarget.style.color = "#f0ebe2"; }}
          >
            Смотреть номера
          </a>
        </div>
      </div>

      {/* Scroll indicator */}
      <div
        style={{
          position: "absolute",
          bottom: 40,
          left: "50%",
          transform: "translateX(-50%)",
          display: "flex",
          flexDirection: "column",
          alignItems: "center",
          gap: 8,
          opacity: 0,
          animation: "fadeSlideUp 1s 1.2s cubic-bezier(0.22,1,0.36,1) forwards",
        }}
      >
        <span style={{ fontFamily: "'DM Mono', monospace", fontSize: 9, letterSpacing: "0.2em", color: "rgba(184,176,164,0.6)", textTransform: "uppercase" }}>Scroll</span>
        <div style={{ width: 1, height: 40, background: "linear-gradient(to bottom, rgba(201,169,110,0.6), transparent)" }} />
      </div>
    </section>
  );
}

/* ─── Stats marquee ───────────────────────────────── */
function Marquee() {
  const items = ["Бесплатный Wi-Fi", "Охраняемая парковка", "Завтрак включён", "Кондиционер во всех номерах", "Организация экскурсий", "Трансфер", "Прачечная", "Хранение багажа"];
  const doubled = [...items, ...items];
  return (
    <div style={{ borderTop: "1px solid rgba(201,169,110,0.14)", borderBottom: "1px solid rgba(201,169,110,0.14)", background: "#111009", padding: "14px 0", overflow: "hidden" }}>
      <div className="marquee-track" style={{ display: "flex", gap: 48, whiteSpace: "nowrap", width: "max-content" }}>
        {doubled.map((item, i) => (
          <span key={i} style={{ display: "flex", alignItems: "center", gap: 20 }}>
            <span style={{ fontFamily: "'DM Mono', monospace", fontSize: 11, letterSpacing: "0.15em", color: "#8a7048", textTransform: "uppercase" }}>{item}</span>
            <span style={{ color: "#c9a96e", fontSize: 14 }}>◆</span>
          </span>
        ))}
      </div>
    </div>
  );
}

/* ─── About ───────────────────────────────────────── */
function About() {
  return (
    <section style={{ padding: "120px 32px", maxWidth: 1280, margin: "0 auto" }}>
      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 80, alignItems: "center" }}>
        <div className="reveal-left">
          <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 11, letterSpacing: "0.2em", color: "#c9a96e", textTransform: "uppercase", marginBottom: 20 }}>
            Об отеле
          </p>
          <h2
            className="gold-line"
            style={{ fontFamily: "'Fraunces', serif", fontSize: "clamp(36px, 4vw, 56px)", fontWeight: 300, lineHeight: 1.15, color: "#f0ebe2", marginBottom: 32 }}
          >
            Место, где
            <em style={{ color: "#c9a96e", fontStyle: "italic" }}> история</em>
            <br />
            встречает комфорт
          </h2>
          <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 16, fontWeight: 300, color: "#b8b0a4", lineHeight: 1.8, marginBottom: 24 }}>
            Отель «Троя» расположен на улице Восстания в Казани — в нескольких минутах от главных достопримечательностей татарской столицы. Мы предлагаем уютные современные номера, внимательный персонал и всё необходимое для комфортного отдыха или деловой поездки.
          </p>
          <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 16, fontWeight: 300, color: "#b8b0a4", lineHeight: 1.8, marginBottom: 40 }}>
            Казань — уникальный город, где слились русская и татарская культуры. Мы поможем вам открыть его через авторские экскурсии, трансферы и индивидуальные маршруты.
          </p>
          <div style={{ display: "flex", gap: 48 }}>
            {[{ num: "5+", label: "Типов номеров" }, { num: "12", label: "Экскурсий" }, { num: "24/7", label: "Сервис" }].map((s) => (
              <div key={s.label}>
                <p style={{ fontFamily: "'Fraunces', serif", fontSize: 40, fontWeight: 300, color: "#c9a96e", lineHeight: 1 }}>{s.num}</p>
                <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 12, color: "#8a7048", letterSpacing: "0.1em", textTransform: "uppercase", marginTop: 6 }}>{s.label}</p>
              </div>
            ))}
          </div>
        </div>
        <div className="reveal-right" style={{ position: "relative" }}>
          <div style={{ aspectRatio: "3/4", borderRadius: 4, overflow: "hidden", background: "#1a1814" }}>
            <img
              src="https://images.unsplash.com/photo-1766777596127-ad864f0d27ad?w=700&h=950&fit=crop&auto=format"
              alt="Казань — Кремль"
              style={{ width: "100%", height: "100%", objectFit: "cover" }}
            />
            <div style={{ position: "absolute", inset: 0, background: "linear-gradient(to bottom, transparent 60%, rgba(12,11,9,0.5))" }} />
          </div>
          {/* Floating card */}
          <div style={{
            position: "absolute",
            bottom: -24,
            left: -32,
            background: "#1a1814",
            border: "1px solid rgba(201,169,110,0.2)",
            padding: "20px 28px",
            borderRadius: 4,
            backdropFilter: "blur(8px)",
          }}>
            <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 10, color: "#c9a96e", letterSpacing: "0.15em", textTransform: "uppercase", marginBottom: 6 }}>Адрес</p>
            <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 14, color: "#f0ebe2", fontWeight: 400 }}>ул. Восстания, 119</p>
            <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 13, color: "#8a7048" }}>Казань, Татарстан</p>
          </div>
        </div>
      </div>
    </section>
  );
}

/* ─── Rooms ───────────────────────────────────────── */
const ROOMS = [
  {
    name: "Standard+",
    subtitle: "Двухместный",
    price: "от 4 000 ₽",
    desc: "Уютный номер с двумя кроватями, всеми удобствами и современной ванной комнатой.",
    features: ["Две кровати", "Телевизор", "Кондиционер", "Мини-бар", "Холодильник"],
    img: "https://images.unsplash.com/photo-1629140727571-9b5c6f6267b4?w=600&h=500&fit=crop&auto=format",
    tag: "Стандарт",
  },
  {
    name: "Standard+",
    subtitle: "Трёхместный",
    price: "от 5 000 ₽",
    desc: "Просторный номер для семьи или компании с тремя раздельными кроватями и плазменным ТВ.",
    features: ["Три кровати", "Плазменный ТВ", "Кондиционер", "Мини-бар", "Холодильник"],
    img: "https://images.unsplash.com/photo-1578898886225-c7c894047899?w=600&h=500&fit=crop&auto=format",
    tag: "Семейный",
  },
  {
    name: "Business Comfort",
    subtitle: "Улучшенный",
    price: "от 5 700 ₽",
    desc: "Двуспальная кровать, полный набор удобств. Идеально для деловых поездок.",
    features: ["Двуспальная кровать", "Телевизор", "Кондиционер", "Телефон", "Мини-бар"],
    img: "https://images.unsplash.com/photo-1590675560125-0d832b9d719e?w=600&h=500&fit=crop&auto=format",
    tag: "Бизнес",
  },
  {
    name: "Business Family",
    subtitle: "Дизайнерский",
    price: "от 6 400 ₽",
    desc: "Уникальный дизайн, халаты, тапочки, большой плазменный ТВ и чайный сервиз.",
    features: ["Дизайнерский интерьер", "Халат и тапочки", "Большой ТВ", "Чайный сервиз", "Мини-кухня"],
    img: "https://images.unsplash.com/photo-1731336478850-6bce7235e320?w=600&h=500&fit=crop&auto=format",
    tag: "Люкс",
  },
  {
    name: "Business Comfort+",
    subtitle: "Премиум",
    price: "от 6 700 ₽",
    desc: "Большая двуспальная кровать, халаты, тапочки и полный комплект премиальных удобств.",
    features: ["Большая кровать", "Халат и тапочки", "Кондиционер", "Полный мини-бар", "Доп. кровать"],
    img: "https://images.unsplash.com/photo-1742821855309-d26c83bdfe1d?w=600&h=500&fit=crop&auto=format",
    tag: "Премиум",
  },
];

function Rooms() {
  const [active, setActive] = useState(0);
  return (
    <section id="rooms" style={{ padding: "100px 0", background: "#0e0d0b" }}>
      <div style={{ maxWidth: 1280, margin: "0 auto", padding: "0 32px" }}>
        <div className="reveal" style={{ marginBottom: 64 }}>
          <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 11, letterSpacing: "0.2em", color: "#c9a96e", textTransform: "uppercase", marginBottom: 16 }}>
            Номерной фонд
          </p>
          <h2
            className="gold-line"
            style={{ fontFamily: "'Fraunces', serif", fontSize: "clamp(32px, 4vw, 52px)", fontWeight: 300, color: "#f0ebe2", lineHeight: 1.2 }}
          >
            Выберите свой номер
          </h2>
        </div>

        {/* Tab selector */}
        <div className="reveal delay-200" style={{ display: "flex", gap: 4, marginBottom: 48, overflowX: "auto", paddingBottom: 4 }}>
          {ROOMS.map((r, i) => (
            <button
              key={i}
              onClick={() => setActive(i)}
              style={{
                fontFamily: "'Outfit', sans-serif",
                fontSize: 13,
                fontWeight: 500,
                padding: "10px 20px",
                borderRadius: 2,
                border: "1px solid",
                borderColor: active === i ? "#c9a96e" : "rgba(201,169,110,0.18)",
                background: active === i ? "rgba(201,169,110,0.1)" : "transparent",
                color: active === i ? "#c9a96e" : "#8a7048",
                cursor: "pointer",
                whiteSpace: "nowrap",
                transition: "all 0.2s ease",
                letterSpacing: "0.04em",
              }}
            >
              {r.tag}
            </button>
          ))}
        </div>

        {/* Active room detail */}
        <div className="reveal delay-300" style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 56, alignItems: "start" }}>
          <div style={{ aspectRatio: "4/3", borderRadius: 4, overflow: "hidden", background: "#1a1814" }}>
            <img
              key={active}
              src={ROOMS[active].img}
              alt={ROOMS[active].name}
              style={{ width: "100%", height: "100%", objectFit: "cover", animation: "fadeSlideUp 0.5s ease forwards" }}
            />
          </div>
          <div style={{ paddingTop: 16 }}>
            <span style={{
              fontFamily: "'DM Mono', monospace",
              fontSize: 10,
              letterSpacing: "0.18em",
              color: "#c9a96e",
              textTransform: "uppercase",
              background: "rgba(201,169,110,0.1)",
              border: "1px solid rgba(201,169,110,0.2)",
              padding: "4px 12px",
              borderRadius: 2,
            }}>
              {ROOMS[active].tag}
            </span>
            <h3 style={{ fontFamily: "'Fraunces', serif", fontSize: 44, fontWeight: 300, color: "#f0ebe2", marginTop: 20, marginBottom: 4, lineHeight: 1.1 }}>
              {ROOMS[active].name}
            </h3>
            <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 14, color: "#8a7048", marginBottom: 24, letterSpacing: "0.04em" }}>
              {ROOMS[active].subtitle}
            </p>
            <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 36, fontWeight: 300, color: "#c9a96e", marginBottom: 24 }}>
              {ROOMS[active].price}
            </p>
            <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 16, fontWeight: 300, color: "#b8b0a4", lineHeight: 1.8, marginBottom: 32 }}>
              {ROOMS[active].desc}
            </p>
            <div style={{ display: "flex", flexWrap: "wrap", gap: 10, marginBottom: 40 }}>
              {ROOMS[active].features.map((f) => (
                <span key={f} style={{
                  fontFamily: "'Outfit', sans-serif",
                  fontSize: 13,
                  color: "#b8b0a4",
                  border: "1px solid rgba(184,176,164,0.2)",
                  padding: "6px 14px",
                  borderRadius: 2,
                }}>
                  {f}
                </span>
              ))}
            </div>
            <div style={{ borderTop: "1px solid rgba(201,169,110,0.12)", paddingTop: 24 }}>
              <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 10, color: "#8a7048", letterSpacing: "0.12em", textTransform: "uppercase", marginBottom: 6 }}>
                Завтрак — 300 ₽ · Доп. кровать — 1 200 ₽
              </p>
              <a
                href="tel:88435644646"
                style={{
                  display: "inline-block",
                  marginTop: 16,
                  fontFamily: "'Outfit', sans-serif",
                  fontSize: 14,
                  fontWeight: 600,
                  color: "#0c0b09",
                  background: "#c9a96e",
                  padding: "14px 40px",
                  borderRadius: 2,
                  textDecoration: "none",
                  letterSpacing: "0.06em",
                  transition: "background 0.2s ease",
                }}
                onMouseEnter={(e) => (e.currentTarget.style.background = "#e2c99a")}
                onMouseLeave={(e) => (e.currentTarget.style.background = "#c9a96e")}
              >
                Забронировать
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

/* ─── Amenities ───────────────────────────────────── */
const AMENITIES = [
  { icon: "🅿", title: "Охраняемая парковка", desc: "Большая видеонаблюдаемая парковка прямо у входа." },
  { icon: "☕", title: "Континентальный завтрак", desc: "Свежий завтрак каждое утро за 300 ₽ с персоны." },
  { icon: "📶", title: "Бесплатный Wi-Fi", desc: "Высокоскоростной интернет по всей территории отеля." },
  { icon: "❄", title: "Кондиционер", desc: "Сплит-система в каждом номере для вашего комфорта." },
  { icon: "🧳", title: "Хранение багажа", desc: "Безопасное хранение вещей и личный сейф в номере." },
  { icon: "🚕", title: "Такси и трансфер", desc: "Организация трансфера и заказ такси 24/7." },
  { icon: "🧺", title: "Прачечная", desc: "Услуги стирки и глажки для постояльцев отеля." },
  { icon: "🗺", title: "Экскурсии", desc: "Организация авторских экскурсий по Казани и Татарстану." },
];

function Amenities() {
  return (
    <section id="amenities" style={{ padding: "100px 32px", maxWidth: 1280, margin: "0 auto" }}>
      <div className="reveal" style={{ marginBottom: 64, display: "flex", justifyContent: "space-between", alignItems: "flex-end", flexWrap: "wrap", gap: 24 }}>
        <div>
          <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 11, letterSpacing: "0.2em", color: "#c9a96e", textTransform: "uppercase", marginBottom: 16 }}>Сервис</p>
          <h2
            className="gold-line"
            style={{ fontFamily: "'Fraunces', serif", fontSize: "clamp(32px, 4vw, 52px)", fontWeight: 300, color: "#f0ebe2", lineHeight: 1.2 }}
          >
            Всё для вашего<br />
            <em style={{ color: "#c9a96e", fontStyle: "italic" }}>комфорта</em>
          </h2>
        </div>
      </div>
      <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(260px, 1fr))", gap: 1, border: "1px solid rgba(201,169,110,0.14)", borderRadius: 4, overflow: "hidden" }}>
        {AMENITIES.map((a, i) => (
          <div
            key={a.title}
            className={`reveal delay-${Math.min((i % 4) * 100 + 100, 400)}`}
            style={{
              padding: "36px 32px",
              background: "#0e0d0b",
              borderRight: "1px solid rgba(201,169,110,0.1)",
              borderBottom: "1px solid rgba(201,169,110,0.1)",
              transition: "background 0.3s ease",
            }}
            onMouseEnter={(e) => ((e.currentTarget as HTMLDivElement).style.background = "#131210")}
            onMouseLeave={(e) => ((e.currentTarget as HTMLDivElement).style.background = "#0e0d0b")}
          >
            <div style={{ fontSize: 28, marginBottom: 16 }}>{a.icon}</div>
            <h3 style={{ fontFamily: "'Outfit', sans-serif", fontSize: 16, fontWeight: 600, color: "#f0ebe2", marginBottom: 10 }}>{a.title}</h3>
            <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 14, fontWeight: 300, color: "#8a7048", lineHeight: 1.7 }}>{a.desc}</p>
          </div>
        ))}
      </div>
    </section>
  );
}

/* ─── Excursions ──────────────────────────────────── */
const EXCURSIONS = [
  { num: "01", title: "Спортивная Казань", desc: "Казань Арена, дворец водных видов спорта, конный комплекс, остров Свияжск, прогулка по Волге." },
  { num: "02", title: "Храм всех религий", desc: "Раифский монастырь, Свияжск, Макарьевская пустынь — речное возвращение." },
  { num: "03", title: "Чистополь", desc: "Купеческая история, музей Пастернака, интерактивная программа." },
  { num: "04", title: "Тетюши", desc: "Мультикультурная история: русские, татары, чуваши, мордва." },
  { num: "05", title: "Йошкар-Ола", desc: "Кремль, набережная Брюгге, комплекс 12 апостолов, площадь Оболенского-Ноготкова." },
  { num: "06", title: "Купеческая Казань", desc: "Петропавловский собор, деревянные купеческие дома, музеи писателей." },
  { num: "07", title: "Елабуга", desc: "Музей Шишкина, мемориал Цветаевой, усадьба Дурова, Чёртово Городище." },
  { num: "08", title: "Болгар", desc: "Памятник XIII–XIV вв.: соборная мечеть, ханская усыпальница, Чёрная и Белая палаты." },
  { num: "09", title: "Ночная Казань", desc: "Подсвеченные достопримечательности, легенды озера Кабан." },
  { num: "10", title: "Обзорная Казань", desc: "Мечеть Кул-Шариф, башня Сююмбике, Благовещенский собор, панорамные виды." },
];

function Excursions() {
  return (
    <section id="excursions" style={{ padding: "100px 0", background: "#0a0908" }}>
      <div style={{ maxWidth: 1280, margin: "0 auto", padding: "0 32px" }}>
        <div className="reveal" style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 80, marginBottom: 64, alignItems: "end" }}>
          <div>
            <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 11, letterSpacing: "0.2em", color: "#c9a96e", textTransform: "uppercase", marginBottom: 16 }}>
              Экскурсии
            </p>
            <h2
              className="gold-line"
              style={{ fontFamily: "'Fraunces', serif", fontSize: "clamp(32px, 4vw, 52px)", fontWeight: 300, color: "#f0ebe2", lineHeight: 1.2 }}
            >
              Откройте Татарстан
              <br />
              <em style={{ color: "#c9a96e", fontStyle: "italic" }}>вместе с нами</em>
            </h2>
          </div>
          <div style={{ paddingBottom: 8 }}>
            <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 16, fontWeight: 300, color: "#b8b0a4", lineHeight: 1.8 }}>
              Мы организуем авторские экскурсии по Казани и всему Татарстану — от ночных прогулок по городу до речных путешествий на остров Свияжск.
            </p>
          </div>
        </div>

        {/* Excursion photo */}
        <div className="reveal delay-200" style={{ marginBottom: 48, borderRadius: 4, overflow: "hidden", height: 320, background: "#1a1814" }}>
          <img
            src="https://images.unsplash.com/photo-1777138388693-8d692c865526?w=1400&h=400&fit=crop&auto=format"
            alt="Казань — вид на крепость"
            style={{ width: "100%", height: "100%", objectFit: "cover" }}
          />
          <div style={{ position: "relative", marginTop: -320, height: 320, background: "linear-gradient(to right, rgba(10,9,8,0.7) 0%, transparent 50%)" }} />
        </div>

        <div style={{ display: "grid", gridTemplateColumns: "repeat(auto-fill, minmax(320px, 1fr))", gap: 0 }}>
          {EXCURSIONS.map((ex, i) => (
            <div
              key={ex.num}
              className={`excursion-card reveal delay-${Math.min((i % 3) * 100 + 100, 400)}`}
              style={{
                padding: "28px 32px",
                borderBottom: "1px solid rgba(201,169,110,0.12)",
                borderRight: "1px solid rgba(201,169,110,0.12)",
                display: "flex",
                gap: 20,
                cursor: "default",
              }}
            >
              <span style={{ fontFamily: "'DM Mono', monospace", fontSize: 12, color: "#c9a96e", opacity: 0.5, marginTop: 3, flexShrink: 0 }}>{ex.num}</span>
              <div>
                <h4 style={{ fontFamily: "'Outfit', sans-serif", fontSize: 15, fontWeight: 600, color: "#f0ebe2", marginBottom: 8 }}>{ex.title}</h4>
                <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 13, fontWeight: 300, color: "#8a7048", lineHeight: 1.6 }}>{ex.desc}</p>
              </div>
            </div>
          ))}
        </div>

        <div className="reveal" style={{ marginTop: 48, textAlign: "center" }}>
          <a
            href="tel:88435644646"
            style={{
              display: "inline-block",
              fontFamily: "'Outfit', sans-serif",
              fontSize: 14,
              fontWeight: 600,
              color: "#c9a96e",
              border: "1px solid rgba(201,169,110,0.4)",
              padding: "14px 40px",
              borderRadius: 2,
              textDecoration: "none",
              letterSpacing: "0.06em",
              transition: "all 0.2s ease",
            }}
            onMouseEnter={(e) => { e.currentTarget.style.background = "rgba(201,169,110,0.1)"; e.currentTarget.style.borderColor = "#c9a96e"; }}
            onMouseLeave={(e) => { e.currentTarget.style.background = "transparent"; e.currentTarget.style.borderColor = "rgba(201,169,110,0.4)"; }}
          >
            Узнать подробности и цены
          </a>
        </div>
      </div>
    </section>
  );
}

/* ─── Reviews ─────────────────────────────────────── */
function Reviews() {
  return (
    <section id="reviews" style={{ padding: "100px 32px", maxWidth: 1280, margin: "0 auto" }}>
      <div className="reveal" style={{ marginBottom: 64, textAlign: "center" }}>
        <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 11, letterSpacing: "0.2em", color: "#c9a96e", textTransform: "uppercase", marginBottom: 16 }}>
          Отзывы гостей
        </p>
        <h2
          className="gold-line-center"
          style={{ fontFamily: "'Fraunces', serif", fontSize: "clamp(32px, 4vw, 52px)", fontWeight: 300, color: "#f0ebe2", lineHeight: 1.2, display: "inline-block" }}
        >
          Что говорят<br /><em style={{ color: "#c9a96e", fontStyle: "italic" }}>наши гости</em>
        </h2>
      </div>
      <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 32 }}>
        {[
          {
            name: "Ольга",
            text: "Очень понравился отель! Персонал встречает с улыбкой, атмосфера уюта и тишины ощущается по всей территории. Чувствуешь себя как дома, но лучше.",
            stars: 5,
          },
          {
            name: "Анастасия",
            text: "Внимательный сервис, уютные номера, чистое постельное бельё и кондиционер в каждом номере. Обязательно вернёмся снова! Рекомендуем всем.",
            stars: 5,
          },
        ].map((r, i) => (
          <div
            key={r.name}
            className={`reveal delay-${i * 200 + 200}`}
            style={{
              background: "#111009",
              border: "1px solid rgba(201,169,110,0.14)",
              borderRadius: 4,
              padding: "40px 40px 36px",
              position: "relative",
            }}
          >
            <div style={{ fontFamily: "'Fraunces', serif", fontSize: 64, color: "rgba(201,169,110,0.15)", lineHeight: 1, position: "absolute", top: 20, right: 32 }}>"</div>
            <div style={{ display: "flex", gap: 4, marginBottom: 20 }}>
              {Array.from({ length: r.stars }).map((_, i) => (
                <span key={i} style={{ color: "#c9a96e", fontSize: 14 }}>★</span>
              ))}
            </div>
            <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 16, fontWeight: 300, color: "#b8b0a4", lineHeight: 1.8, marginBottom: 28, fontStyle: "italic" }}>
              "{r.text}"
            </p>
            <div style={{ borderTop: "1px solid rgba(201,169,110,0.12)", paddingTop: 20, display: "flex", alignItems: "center", gap: 12 }}>
              <div style={{ width: 36, height: 36, borderRadius: "50%", background: "rgba(201,169,110,0.15)", display: "flex", alignItems: "center", justifyContent: "center" }}>
                <span style={{ fontFamily: "'Fraunces', serif", fontSize: 16, color: "#c9a96e" }}>{r.name[0]}</span>
              </div>
              <div>
                <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 14, fontWeight: 500, color: "#f0ebe2" }}>{r.name}</p>
                <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 10, color: "#8a7048", letterSpacing: "0.1em" }}>ГОСТЬ ОТЕЛЯ</p>
              </div>
            </div>
          </div>
        ))}
      </div>
    </section>
  );
}

/* ─── Contact ─────────────────────────────────────── */
function Contact() {
  return (
    <section id="contact" style={{ padding: "100px 0", background: "#080807" }}>
      <div style={{ maxWidth: 1280, margin: "0 auto", padding: "0 32px" }}>
        <div style={{ display: "grid", gridTemplateColumns: "1fr 1fr", gap: 80, alignItems: "start" }}>
          <div className="reveal-left">
            <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 11, letterSpacing: "0.2em", color: "#c9a96e", textTransform: "uppercase", marginBottom: 16 }}>
              Контакты
            </p>
            <h2
              className="gold-line"
              style={{ fontFamily: "'Fraunces', serif", fontSize: "clamp(32px, 4vw, 52px)", fontWeight: 300, color: "#f0ebe2", lineHeight: 1.2, marginBottom: 48 }}
            >
              Свяжитесь<br /><em style={{ color: "#c9a96e", fontStyle: "italic" }}>с нами</em>
            </h2>

            <div style={{ display: "flex", flexDirection: "column", gap: 32 }}>
              {[
                {
                  label: "Бронирование",
                  values: ["8 (843) 564-46-46", "8 (904) 678-56-00"],
                  type: "tel",
                },
                {
                  label: "Бухгалтерия (9:00–15:00)",
                  values: ["8 (843) 564-83-19"],
                  type: "tel",
                },
                {
                  label: "Email",
                  values: ["hoteltroya@mail.ru"],
                  type: "email",
                },
                {
                  label: "Адрес",
                  values: ["420095, РФ, РТ, г. Казань", "ул. Восстания, 119"],
                  type: "text",
                },
              ].map((c) => (
                <div key={c.label} style={{ borderBottom: "1px solid rgba(201,169,110,0.1)", paddingBottom: 28 }}>
                  <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 10, color: "#8a7048", letterSpacing: "0.15em", textTransform: "uppercase", marginBottom: 10 }}>
                    {c.label}
                  </p>
                  {c.values.map((v) =>
                    c.type === "tel" ? (
                      <a key={v} href={`tel:${v.replace(/\D/g, "")}`} style={{ display: "block", fontFamily: "'Fraunces', serif", fontSize: 24, fontWeight: 300, color: "#f0ebe2", textDecoration: "none", transition: "color 0.2s ease", marginBottom: 4 }}
                        onMouseEnter={(e) => (e.currentTarget.style.color = "#c9a96e")}
                        onMouseLeave={(e) => (e.currentTarget.style.color = "#f0ebe2")}
                      >{v}</a>
                    ) : c.type === "email" ? (
                      <a key={v} href={`mailto:${v}`} style={{ display: "block", fontFamily: "'Outfit', sans-serif", fontSize: 18, color: "#f0ebe2", textDecoration: "none", transition: "color 0.2s ease" }}
                        onMouseEnter={(e) => (e.currentTarget.style.color = "#c9a96e")}
                        onMouseLeave={(e) => (e.currentTarget.style.color = "#f0ebe2")}
                      >{v}</a>
                    ) : (
                      <p key={v} style={{ fontFamily: "'Outfit', sans-serif", fontSize: 16, fontWeight: 300, color: "#b8b0a4", lineHeight: 1.6 }}>{v}</p>
                    )
                  )}
                </div>
              ))}
            </div>
          </div>

          <div className="reveal-right" style={{ display: "flex", flexDirection: "column", gap: 24 }}>
            {/* Map placeholder */}
            <div style={{ borderRadius: 4, overflow: "hidden", background: "#1a1814", border: "1px solid rgba(201,169,110,0.14)", height: 340, position: "relative" }}>
              <img
                src="https://images.unsplash.com/photo-1764470729410-4336faf28051?w=700&h=400&fit=crop&auto=format"
                alt="Казань"
                style={{ width: "100%", height: "100%", objectFit: "cover", opacity: 0.6 }}
              />
              <div style={{ position: "absolute", inset: 0, display: "flex", alignItems: "center", justifyContent: "center", flexDirection: "column", gap: 12 }}>
                <div style={{ width: 16, height: 16, borderRadius: "50%", background: "#c9a96e", boxShadow: "0 0 0 6px rgba(201,169,110,0.3)" }} />
                <span style={{ fontFamily: "'Outfit', sans-serif", fontSize: 14, fontWeight: 500, color: "#f0ebe2", background: "rgba(12,11,9,0.8)", padding: "8px 16px", borderRadius: 2 }}>
                  ул. Восстания, 119
                </span>
              </div>
            </div>

            <a
              href="tel:88435644646"
              style={{
                display: "block",
                textAlign: "center",
                fontFamily: "'Outfit', sans-serif",
                fontSize: 16,
                fontWeight: 600,
                color: "#0c0b09",
                background: "#c9a96e",
                padding: "18px",
                borderRadius: 2,
                textDecoration: "none",
                letterSpacing: "0.06em",
                transition: "background 0.2s ease",
              }}
              onMouseEnter={(e) => (e.currentTarget.style.background = "#e2c99a")}
              onMouseLeave={(e) => (e.currentTarget.style.background = "#c9a96e")}
            >
              Позвонить и забронировать
            </a>

            <div style={{ background: "#111009", border: "1px solid rgba(201,169,110,0.12)", borderRadius: 4, padding: "20px 24px" }}>
              <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 10, color: "#8a7048", letterSpacing: "0.12em", textTransform: "uppercase", marginBottom: 8 }}>
                Режим работы администрации
              </p>
              <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 15, color: "#f0ebe2" }}>
                Круглосуточно, 24/7
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

/* ─── Footer ──────────────────────────────────────── */
function Footer() {
  return (
    <footer style={{ borderTop: "1px solid rgba(201,169,110,0.12)", padding: "32px", background: "#080807" }}>
      <div style={{ maxWidth: 1280, margin: "0 auto", display: "flex", justifyContent: "space-between", alignItems: "center", flexWrap: "wrap", gap: 16 }}>
        <a href="#" style={{ fontFamily: "'Fraunces', serif", fontSize: 18, fontWeight: 400, color: "#c9a96e", textDecoration: "none", letterSpacing: "0.04em" }}>
          Отель <span style={{ color: "#f0ebe2" }}>Троя</span>
        </a>
        <p style={{ fontFamily: "'DM Mono', monospace", fontSize: 10, color: "#8a7048", letterSpacing: "0.12em", textTransform: "uppercase" }}>
          ООО «ТРОЯ» · ИНН 1657131940 · Казань, ул. Восстания, 119
        </p>
        <p style={{ fontFamily: "'Outfit', sans-serif", fontSize: 12, color: "#8a7048" }}>© 2026 Все права защищены</p>
      </div>
    </footer>
  );
}

/* ─── App ─────────────────────────────────────────── */
export default function App() {
  useReveal();

  return (
    <div style={{ minHeight: "100vh", background: "#0c0b09" }}>
      <Nav />
      <Hero />
      <Marquee />
      <About />
      <Rooms />
      <Amenities />
      <Excursions />
      <Reviews />
      <Contact />
      <Footer />
    </div>
  );
}
