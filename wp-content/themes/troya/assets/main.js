/* ─── Data from WordPress (TroyaData) with fallbacks ─ */
const WP = window.TroyaData || {};
const ROOMS =
  Array.isArray(WP.rooms) && WP.rooms.length
    ? WP.rooms
    : [
        {
          name: "Standard+",
          subtitle: "Двухместный",
          price: "от 4 000 ₽",
          desc: "Уютный номер с двумя кроватями.",
          features: ["Две кровати", "Телевизор", "Кондиционер"],
          img: (WP.homeUrl || "/") + "wp-content/themes/troya/assets/photos/room-1.jpg",
          tag: "Стандарт",
          href: WP.roomsUrl || "#",
        },
      ];

const AMENITIES = Array.isArray(WP.amenities) ? WP.amenities : [];
const REVIEWS = Array.isArray(WP.reviews) && WP.reviews.length
  ? WP.reviews
  : [
      { name: "Ольга", text: "Очень понравился отель! Персонал встречает с улыбкой." },
      { name: "Анастасия", text: "Внимательный сервис, уютные номера и чистое бельё." },
    ];
const EXCURSIONS = Array.isArray(WP.excursions) ? WP.excursions : [];

function pad(n) {
  return String(n).padStart(2, "0");
}

/* ─── Modal ──────────────────────────────────────── */
const modal = document.getElementById("booking-modal");
const modalRoom = document.getElementById("modal-room");
const roomField = document.getElementById("room-field");
const roomTypeInput = document.getElementById("room-type");
const bookingForm = document.getElementById("booking-form");
const modalSuccess = document.getElementById("modal-success");

let modalClosing = false;

function reachGoal(name) {
  try {
    if (typeof window.yaCounter50056810 !== "undefined") {
      window.yaCounter50056810.reachGoal(name);
    }
  } catch (e) {}
}

function openModal(roomName) {
  if (!modal || modalClosing) return;

  reachGoal("CLKBTN");

  modal.classList.remove("is-closing");
  bookingForm.hidden = false;
  modalSuccess.hidden = true;
  bookingForm.reset();

  if (roomName) {
    modalRoom.hidden = false;
    modalRoom.textContent = roomName;
    roomField.hidden = false;
    roomTypeInput.value = roomName;
  } else {
    modalRoom.hidden = true;
    modalRoom.textContent = "";
    roomField.hidden = true;
    roomTypeInput.value = "";
  }

  void modal.offsetWidth;
  modal.classList.add("is-open");
  modal.setAttribute("aria-hidden", "false");
  document.body.classList.add("modal-open");
}

function closeModal() {
  if (!modal || !modal.classList.contains("is-open") || modalClosing) return;

  modalClosing = true;
  modal.classList.add("is-closing");
  modal.classList.remove("is-open");

  const dialog = modal.querySelector(".modal__dialog");
  const onEnd = (e) => {
    if (e.target !== dialog) return;
    dialog.removeEventListener("transitionend", onEnd);
    finishClose();
  };

  dialog.addEventListener("transitionend", onEnd);
  setTimeout(finishClose, 400);
}

function finishClose() {
  if (!modalClosing) return;
  modalClosing = false;
  modal.classList.remove("is-closing");
  modal.setAttribute("aria-hidden", "true");
  document.body.classList.remove("modal-open");
}

document.querySelectorAll("[data-open-modal]").forEach((btn) => {
  btn.addEventListener("click", () => {
    setMenuOpen(false);
    openModal(btn.dataset.room || undefined);
  });
});

document.querySelectorAll("[data-close-modal]").forEach((el) => {
  el.addEventListener("click", closeModal);
});

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") {
    closeModal();
    setMenuOpen(false);
  }
});

bookingForm?.addEventListener("submit", async (e) => {
  e.preventDefault();
  const errorEl = document.getElementById("modal-error");
  if (errorEl) {
    errorEl.hidden = true;
    errorEl.textContent = "";
  }

  const submitBtn = bookingForm.querySelector('[type="submit"]');
  if (submitBtn) submitBtn.disabled = true;

  const fd = new FormData(bookingForm);
  fd.append("action", "troya_submit_booking");
  fd.append("nonce", WP.nonce || "");

  try {
    if (!WP.ajaxUrl) throw new Error("Форма временно недоступна");

    const res = await fetch(WP.ajaxUrl, {
      method: "POST",
      body: fd,
      credentials: "same-origin",
    });
    const json = await res.json();

    if (!json?.success) {
      throw new Error(json?.data?.message || "Не удалось отправить заявку");
    }

    reachGoal("ORDER");
    bookingForm.hidden = true;
    modalSuccess.hidden = false;
  } catch (err) {
    if (errorEl) {
      errorEl.hidden = false;
      errorEl.textContent = err.message || "Ошибка отправки";
    }
  } finally {
    if (submitBtn) submitBtn.disabled = false;
  }
});

/* ─── Header / creative burger ───────────────────── */
const header = document.getElementById("header");
const burger = document.getElementById("burger");
const menuOverlay = document.getElementById("menu-overlay");
const nav = document.getElementById("nav");
const burgerLabel = burger?.querySelector(".burger__label");

function setMenuOpen(open) {
  menuOverlay?.classList.toggle("is-open", open);
  burger?.classList.toggle("is-open", open);
  burger?.setAttribute("aria-expanded", String(open));
  menuOverlay?.setAttribute("aria-hidden", String(!open));
  document.body.classList.toggle("menu-open", open);
  if (burgerLabel) {
    burgerLabel.textContent = open
      ? burgerLabel.dataset.close || "Закрыть"
      : burgerLabel.dataset.open || "Меню";
  }
}

burger?.addEventListener("click", () => {
  setMenuOpen(!menuOverlay?.classList.contains("is-open"));
});

document.getElementById("menu-close")?.addEventListener("click", () => {
  setMenuOpen(false);
});

nav?.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => setMenuOpen(false));
});

window.addEventListener(
  "scroll",
  () => {
    header?.classList.toggle("is-scrolled", window.scrollY > 60);
  },
  { passive: true }
);

window.addEventListener("resize", () => {
  if (window.innerWidth > 900) setMenuOpen(false);
});

/* ─── Rooms stacked slider ───────────────────────── */
const roomsStage = document.getElementById("rooms-stage");
const roomTag = document.getElementById("room-tag");
const roomName = document.getElementById("room-name");
const roomSubtitle = document.getElementById("room-subtitle");
const roomPrice = document.getElementById("room-price");
const roomDesc = document.getElementById("room-desc");
const roomFeatures = document.getElementById("room-features");
const roomBook = document.getElementById("room-book");
const roomMore = document.getElementById("room-more");
const roomsCurrent = document.getElementById("rooms-current");
const roomsTotal = document.getElementById("rooms-total");

let activeRoom = 0;

function updateRoomPanel(index) {
  const room = ROOMS[index];
  roomTag.textContent = room.tag;
  roomName.textContent = room.name;
  roomSubtitle.textContent = room.subtitle;
  roomPrice.textContent = room.price;
  roomDesc.textContent = room.desc;
  roomFeatures.innerHTML = room.features.map((f) => `<span>${f}</span>`).join("");
  if (roomsCurrent) roomsCurrent.textContent = pad(index + 1);
  if (roomMore) roomMore.href = room.href || "rooms.html";
}

function renderRoomSlides() {
  if (!roomsStage) return;
  roomsStage.innerHTML = ROOMS.map(
    (room, i) => `
      <article class="room-slide" data-index="${i}">
        <img src="${room.img}" alt="${room.name}" draggable="false" loading="${i === 0 ? "eager" : "lazy"}" decoding="async" />
      </article>
    `
  ).join("");
  if (roomsTotal) roomsTotal.textContent = pad(ROOMS.length);
  setActiveRoom(0);
}

function setActiveRoom(index) {
  activeRoom = (index + ROOMS.length) % ROOMS.length;
  const slides = roomsStage?.querySelectorAll(".room-slide");
  slides?.forEach((slide, i) => {
    slide.classList.remove("is-active", "is-next", "is-prev", "is-far");
    if (i === activeRoom) slide.classList.add("is-active");
    else if (i === (activeRoom + 1) % ROOMS.length) slide.classList.add("is-next");
    else if (i === (activeRoom - 1 + ROOMS.length) % ROOMS.length) slide.classList.add("is-prev");
    else slide.classList.add("is-far");
  });
  updateRoomPanel(activeRoom);
}

function bindSwipe(el, onPrev, onNext) {
  if (!el) return;

  const THRESHOLD = 36;
  let startX = 0;
  let startY = 0;
  let tracking = false;
  let axis = null;

  const begin = (x, y) => {
    tracking = true;
    axis = null;
    startX = x;
    startY = y;
  };

  const updateAxis = (x, y) => {
    if (!tracking || axis) return;
    const dx = x - startX;
    const dy = y - startY;
    if (Math.abs(dx) < 10 && Math.abs(dy) < 10) return;
    axis = Math.abs(dx) > Math.abs(dy) ? "x" : "y";
  };

  const finish = (x) => {
    if (!tracking) return;
    const dx = x - startX;
    const shouldGo = axis === "x" && Math.abs(dx) >= THRESHOLD;
    tracking = false;
    axis = null;
    if (!shouldGo) return;
    if (dx < 0) onNext();
    else onPrev();
  };

  el.addEventListener(
    "touchstart",
    (e) => {
      const t = e.changedTouches[0];
      begin(t.clientX, t.clientY);
    },
    { passive: true }
  );

  el.addEventListener(
    "touchmove",
    (e) => {
      const t = e.changedTouches[0];
      updateAxis(t.clientX, t.clientY);
    },
    { passive: true }
  );

  el.addEventListener(
    "touchend",
    (e) => {
      const t = e.changedTouches[0];
      finish(t.clientX);
    },
    { passive: true }
  );

  el.addEventListener("pointerdown", (e) => {
    if (e.pointerType === "touch") return;
    if (e.button !== 0) return;
    begin(e.clientX, e.clientY);
    el.setPointerCapture?.(e.pointerId);
  });

  el.addEventListener("pointermove", (e) => {
    if (e.pointerType === "touch") return;
    updateAxis(e.clientX, e.clientY);
  });

  el.addEventListener("pointerup", (e) => {
    if (e.pointerType === "touch") return;
    finish(e.clientX);
  });

  el.addEventListener("pointercancel", () => {
    tracking = false;
    axis = null;
  });
}

renderRoomSlides();

const ROOM_AUTOPLAY_MS = 6000;
const roomsSlider = document.getElementById("rooms-slider");
let roomAutoplayTimer = null;

function stopRoomAutoplay() {
  if (roomAutoplayTimer) {
    clearInterval(roomAutoplayTimer);
    roomAutoplayTimer = null;
  }
}

function startRoomAutoplay() {
  stopRoomAutoplay();
  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
  roomAutoplayTimer = setInterval(() => {
    setActiveRoom(activeRoom + 1);
  }, ROOM_AUTOPLAY_MS);
}

function stepRoom(delta) {
  setActiveRoom(activeRoom + delta);
  startRoomAutoplay();
}

document.getElementById("rooms-prev")?.addEventListener("click", () => stepRoom(-1));
document.getElementById("rooms-next")?.addEventListener("click", () => stepRoom(1));
bindSwipe(roomsStage, () => stepRoom(-1), () => stepRoom(1));

roomsSlider?.addEventListener("pointerenter", (e) => {
  if (e.pointerType === "mouse") stopRoomAutoplay();
});
roomsSlider?.addEventListener("pointerleave", (e) => {
  if (e.pointerType === "mouse") startRoomAutoplay();
});

document.addEventListener("visibilitychange", () => {
  if (document.hidden) stopRoomAutoplay();
  else startRoomAutoplay();
});

if ("IntersectionObserver" in window && roomsSlider) {
  const roomsInView = new IntersectionObserver(
    ([entry]) => {
      if (entry.isIntersecting) startRoomAutoplay();
      else stopRoomAutoplay();
    },
    { threshold: 0.35 }
  );
  roomsInView.observe(roomsSlider);
} else {
  startRoomAutoplay();
}

roomBook?.addEventListener("click", () => {
  const room = ROOMS[activeRoom];
  openModal(`${room.name} — ${room.subtitle}`);
});

/* ─── Amenities mosaic ───────────────────────────── */
const amenitiesGrid = document.getElementById("amenities-grid");
if (amenitiesGrid) {
  amenitiesGrid.innerHTML = AMENITIES.map((a, i) => {
    const delay = Math.min((i % 4) * 100 + 100, 400);
    return `
      <article class="amenity reveal delay-${delay}" data-index="${pad(i + 1)}">
        <div class="amenity__icon">
          <img src="${a.icon}" alt="" width="40" height="40" loading="lazy" />
        </div>
        <h3>${a.title}</h3>
        <p>${a.desc}</p>
      </article>
    `;
  }).join("");
}

/* ─── Reviews deck slider ────────────────────────── */
const reviewsDeck = document.getElementById("reviews-deck");
let activeReview = 0;

function renderReviews() {
  if (!reviewsDeck) return;
  reviewsDeck.innerHTML = REVIEWS.map(
    (r, i) => `
      <article class="review-card" data-index="${i}">
        <div class="review-card__mark">“</div>
        <div class="review-card__stars">★★★★★</div>
        <p class="review-card__text">${r.text}</p>
        <div class="review-card__author">
          <div class="review-card__avatar">${r.name[0]}</div>
          <div>
            <p class="review-card__name">${r.name}</p>
            <p class="review-card__role">ГОСТЬ ОТЕЛЯ</p>
          </div>
        </div>
      </article>
    `
  ).join("");
  setActiveReview(0);
}

function setActiveReview(index) {
  activeReview = (index + REVIEWS.length) % REVIEWS.length;
  reviewsDeck?.querySelectorAll(".review-card").forEach((card, i) => {
    card.classList.remove("is-active", "is-next", "is-prev", "is-far");
    if (i === activeReview) card.classList.add("is-active");
    else if (i === (activeReview + 1) % REVIEWS.length) card.classList.add("is-next");
    else if (i === (activeReview - 1 + REVIEWS.length) % REVIEWS.length) card.classList.add("is-prev");
    else card.classList.add("is-far");
  });
}

renderReviews();

document.getElementById("reviews-prev")?.addEventListener("click", () => setActiveReview(activeReview - 1));
document.getElementById("reviews-next")?.addEventListener("click", () => setActiveReview(activeReview + 1));
bindSwipe(reviewsDeck, () => setActiveReview(activeReview - 1), () => setActiveReview(activeReview + 1));

/* ─── Excursions ─────────────────────────────────── */
const excursionsGrid = document.getElementById("excursions-grid");
if (excursionsGrid) {
  excursionsGrid.innerHTML = EXCURSIONS.map((ex, i) => {
    const delay = Math.min((i % 3) * 100 + 100, 400);
    return `
      <div class="excursion-card reveal delay-${delay}">
        <span class="excursion-card__num">${ex.num}</span>
        <div>
          <h4>${ex.title}</h4>
          <p>${ex.desc}</p>
        </div>
      </div>
    `;
  }).join("");
}

/* ─── Scroll reveal ──────────────────────────────── */
function initReveal() {
  const els = document.querySelectorAll(".reveal, .reveal-left, .reveal-right");
  if (!("IntersectionObserver" in window)) {
    els.forEach((el) => el.classList.add("visible"));
    return;
  }

  const obs = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("visible");
          obs.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.12 }
  );

  els.forEach((el) => obs.observe(el));
}

initReveal();

/* ─── Hero video autoplay ────────────────────────── */
(function initHeroVideo() {
  const video = document.getElementById("hero-video");
  const fallback = document.querySelector(".hero__img--fallback");
  if (!video) return;

  video.loop = false;
  video.removeAttribute("loop");

  const tryPlay = () => {
    video.muted = true;
    video.loop = false;
    const playPromise = video.play();
    if (playPromise?.catch) {
      playPromise.catch(() => {
        if (fallback) fallback.hidden = false;
      });
    }
  };

  video.addEventListener("ended", () => {
    video.pause();
    try {
      video.currentTime = Math.max((video.duration || 0) - 0.05, 0);
    } catch (_) {
      /* ignore */
    }
  });

  video.addEventListener("error", () => {
    if (fallback) fallback.hidden = false;
    video.hidden = true;
  });

  if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) {
    video.removeAttribute("autoplay");
    video.pause();
    if (fallback) fallback.hidden = false;
    return;
  }

  if (video.readyState >= 2) tryPlay();
  else video.addEventListener("canplay", tryPlay, { once: true });

  document.addEventListener(
    "visibilitychange",
    () => {
      if (!document.hidden) tryPlay();
    },
    { passive: true }
  );
})();
