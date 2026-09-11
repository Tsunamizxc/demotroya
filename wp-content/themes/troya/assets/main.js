/* ─── Data from WordPress (TroyaData) with fallbacks ─ */
const WP = window.TroyaData || {};
const ROOMS =
  Array.isArray(WP.rooms) && WP.rooms.length
    ? WP.rooms
    : [
        {
          name: "2-х местный Стандарт",
          subtitle: "Двухместный",
          price: "от 4 000 ₽",
          desc: "Уютный номер с двумя раздельными кроватями, мини-холодильником и современным санузлом.",
          features: ["Две кровати", "Телевизор", "Кондиционер", "Мини-холодильник", "Современный санузел"],
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

/* ─── Rooms video slider ─────────────────────────── */
const roomsSection = document.querySelector("[data-rooms-slider]");
const roomsVideosEl = document.getElementById("rooms-videos");
const roomsCard = document.getElementById("rooms-card");
const roomCardImg = document.getElementById("room-card-img");
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
const roomsProgress = document.getElementById("rooms-progress");
const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

let activeRoom = 0;
let roomsVideos = [];
let roomsInView = false;
let roomsHasPlayed = false;

function updateRoomPanel(index, animate) {
  const room = ROOMS[index];
  if (!room) return;

  const apply = () => {
    if (roomTag) roomTag.textContent = room.tag || "";
    if (roomName) roomName.textContent = room.name || "";
    if (roomSubtitle) roomSubtitle.textContent = room.subtitle || "";
    if (roomPrice) roomPrice.textContent = room.price || "";
    if (roomDesc) roomDesc.textContent = room.desc || "";
    if (roomFeatures) {
      roomFeatures.innerHTML = (room.features || []).map((f) => `<span>${f}</span>`).join("");
    }
    if (roomsCurrent) roomsCurrent.textContent = pad(index + 1);
    if (roomMore) roomMore.href = room.href || WP.roomsUrl || "#";
    if (roomBook && roomBook.tagName === "A") {
      roomBook.href = room.bookUrl || WP.bookingUrl || roomBook.getAttribute("href") || "#";
    }
    if (roomCardImg) {
      roomCardImg.src = room.img || "";
      roomCardImg.alt = room.name || "";
    }

    roomsProgress?.querySelectorAll("[data-room-dot]").forEach((dot, i) => {
      dot.classList.toggle("is-active", i === index);
      dot.setAttribute("aria-current", i === index ? "true" : "false");
    });
  };

  if (!animate || reduceMotion || !roomsCard) {
    apply();
    roomsCard?.classList.add("is-visible");
    return;
  }

  roomsCard.classList.add("is-switching");
  window.setTimeout(() => {
    apply();
    roomsCard.classList.remove("is-switching");
    roomsCard.classList.add("is-visible");
  }, 220);
}

function pauseAllRoomsVideos() {
  roomsVideos.forEach((video) => {
    if (video.tagName === "VIDEO" && !video.paused) video.pause();
  });
}

function prepareRoomVideoEl(video) {
  if (!video || video.tagName !== "VIDEO") return;
  video.muted = true;
  video.defaultMuted = true;
  video.playsInline = true;
  video.setAttribute("muted", "");
  video.setAttribute("playsinline", "");
  video.setAttribute("webkit-playsinline", "");
  // Inline src is more reliable on iOS than nested <source>.
  const source = video.querySelector("source");
  if (source?.src && !video.getAttribute("src")) {
    video.src = source.src;
  }
}

function setActiveRoomVideo(index) {
  roomsVideos.forEach((item, i) => {
    const on = i === index;
    item.classList.toggle("is-active", on);
    if (item.tagName !== "VIDEO") return;
    prepareRoomVideoEl(item);
    item.preload = Math.abs(i - index) <= 1 ? "auto" : "metadata";
    if (!on && !item.paused) item.pause();
  });
}

function playRoomVideo(index, fromStart) {
  const video = roomsVideos[index];
  if (!video || video.tagName !== "VIDEO") {
    setActiveRoomVideo(index);
    return;
  }

  setActiveRoomVideo(index);
  prepareRoomVideoEl(video);

  if (reduceMotion) {
    try {
      if (Number.isFinite(video.duration) && video.duration > 0) {
        video.currentTime = Math.max(video.duration - 0.05, 0);
      }
    } catch (e) {}
    video.pause();
    return;
  }

  let started = false;
  const tryPlay = () => {
    if (started) return;
    started = true;
    video.muted = true;
    const playPromise = video.play();
    if (playPromise && typeof playPromise.catch === "function") {
      playPromise.catch(() => {
        started = false;
      });
    }
  };

  const start = () => {
    if (fromStart && video.currentTime > 0.02) {
      const onSeeked = () => {
        video.removeEventListener("seeked", onSeeked);
        tryPlay();
      };
      video.addEventListener("seeked", onSeeked, { once: true });
      try {
        video.currentTime = 0;
      } catch (e) {
        tryPlay();
      }
      // iOS can skip seeked if already near 0 / not ready.
      window.setTimeout(() => {
        if (video.paused) tryPlay();
      }, 250);
      return;
    }
    tryPlay();
  };

  if (video.readyState >= 2) {
    start();
    return;
  }

  const onReady = () => start();
  video.addEventListener("loadeddata", onReady, { once: true });
  video.addEventListener("canplay", onReady, { once: true });
  try {
    video.load();
  } catch (e) {}
  window.setTimeout(() => {
    if (video.paused) start();
  }, 600);
}

function goToRoom(index, { play = true, animate = true } = {}) {
  if (!ROOMS.length) return;

  const next = ((index % ROOMS.length) + ROOMS.length) % ROOMS.length;
  if (next === activeRoom && !play) return;

  activeRoom = next;
  updateRoomPanel(activeRoom, animate);

  if (play) {
    // User navigation / in-view play must not depend on IO flags alone (mobile).
    roomsInView = true;
    roomsHasPlayed = true;
    playRoomVideo(activeRoom, true);
  } else {
    setActiveRoomVideo(activeRoom);
    pauseAllRoomsVideos();
  }
}

function stepRoom(delta) {
  goToRoom(activeRoom + delta, { play: true, animate: true });
}

function initRoomsSlider() {
  if (!roomsSection || !roomsVideosEl || !ROOMS.length) return;

  if (roomsTotal) roomsTotal.textContent = pad(ROOMS.length);

  roomsVideosEl.innerHTML = ROOMS.map((room, i) => {
    if (room.video) {
      return `<video class="rooms-story__video" data-index="${i}" muted defaultMuted playsinline webkit-playsinline preload="${i === 0 ? "auto" : "metadata"}" poster="${room.img || ""}" src="${room.video}">
        <source src="${room.video}" type="video/mp4" />
      </video>`;
    }
    return `<div class="rooms-story__video rooms-story__video--still" data-index="${i}" style="background-image:url('${room.img || ""}')"></div>`;
  }).join("");

  roomsVideos = Array.from(roomsVideosEl.querySelectorAll(".rooms-story__video"));

  roomsVideos.forEach((video) => {
    if (video.tagName !== "VIDEO") return;
    prepareRoomVideoEl(video);
    video.addEventListener("ended", () => {
      // Keep the natural last frame — seeking back causes a visible jerk.
      video.pause();
    });
  });

  if (roomsProgress) {
    roomsProgress.innerHTML = ROOMS.map(
      (room, i) =>
        `<button type="button" class="rooms-story__dot" data-room-dot data-index="${i}" aria-label="${room.name || `Номер ${i + 1}`}"></button>`
    ).join("");

    roomsProgress.querySelectorAll("[data-room-dot]").forEach((dot) => {
      dot.addEventListener("click", (e) => {
        e.preventDefault();
        e.stopPropagation();
        goToRoom(Number(dot.dataset.index) || 0, { play: true, animate: true });
      });
    });
  }

  document.getElementById("rooms-prev")?.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    stepRoom(-1);
  });
  document.getElementById("rooms-next")?.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    stepRoom(1);
  });

  const swipeTarget = roomsSection.querySelector(".rooms-story__frame") || roomsSection;
  bindSwipe(swipeTarget, () => stepRoom(-1), () => stepRoom(1));

  updateRoomPanel(0, false);
  roomsVideos.forEach((item, i) => item.classList.toggle("is-active", i === 0));

  const unlockRoomsPlayback = () => {
    roomsInView = true;
    roomsHasPlayed = true;
    playRoomVideo(activeRoom, true);
  };

  // First gesture unlocks autoplay policies on iOS/Android.
  roomsSection.addEventListener(
    "pointerdown",
    () => {
      const video = roomsVideos[activeRoom];
      if (video && video.tagName === "VIDEO" && video.paused) {
        unlockRoomsPlayback();
      }
    },
    { passive: true }
  );

  if ("IntersectionObserver" in window) {
    const mobile = window.matchMedia("(max-width: 900px)").matches;
    const io = new IntersectionObserver(
      ([entry]) => {
        const minRatio = mobile ? 0.12 : 0.35;
        roomsInView = entry.isIntersecting && entry.intersectionRatio >= minRatio;
        if (roomsInView) {
          if (!roomsHasPlayed) {
            roomsHasPlayed = true;
            playRoomVideo(activeRoom, true);
          } else {
            const video = roomsVideos[activeRoom];
            if (
              video &&
              video.tagName === "VIDEO" &&
              video.paused &&
              (!Number.isFinite(video.duration) || video.currentTime < video.duration - 0.15)
            ) {
              playRoomVideo(activeRoom, false);
            }
          }
        } else {
          pauseAllRoomsVideos();
        }
      },
      {
        threshold: mobile ? [0.08, 0.15, 0.25, 0.4] : [0.35, 0.55],
        rootMargin: mobile ? "40px 0px 40px 0px" : "0px",
      }
    );
    io.observe(roomsSection);
  } else {
    unlockRoomsPlayback();
  }

  document.addEventListener("visibilitychange", () => {
    if (document.hidden) pauseAllRoomsVideos();
    else if (roomsInView) {
      const video = roomsVideos[activeRoom];
      if (
        video &&
        video.tagName === "VIDEO" &&
        video.paused &&
        (!Number.isFinite(video.duration) || video.currentTime < video.duration - 0.15)
      ) {
        playRoomVideo(activeRoom, false);
      }
    }
  });
}

initRoomsSlider();

roomBook?.addEventListener("click", () => {
  reachGoal("CLKBTN");
});

function bindSwipe(el, onPrev, onNext) {
  if (!el) return;

  const THRESHOLD = 36;
  const IGNORE = "a, button, input, textarea, select, label, [data-no-swipe]";
  let startX = 0;
  let startY = 0;
  let tracking = false;
  let axis = null;

  const isIgnored = (target) => {
    if (!(target instanceof Element)) return true;
    return Boolean(target.closest(IGNORE));
  };

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
      if (isIgnored(e.target)) return;
      const t = e.changedTouches[0];
      begin(t.clientX, t.clientY);
    },
    { passive: true }
  );

  el.addEventListener(
    "touchmove",
    (e) => {
      if (!tracking) return;
      const t = e.changedTouches[0];
      updateAxis(t.clientX, t.clientY);
    },
    { passive: true }
  );

  el.addEventListener(
    "touchend",
    (e) => {
      if (!tracking) return;
      const t = e.changedTouches[0];
      finish(t.clientX);
    },
    { passive: true }
  );

  el.addEventListener("pointerdown", (e) => {
    if (e.pointerType === "touch") return;
    if (e.button !== 0) return;
    if (isIgnored(e.target)) return;
    begin(e.clientX, e.clientY);
    el.setPointerCapture?.(e.pointerId);
  });

  el.addEventListener("pointermove", (e) => {
    if (e.pointerType === "touch") return;
    if (!tracking) return;
    updateAxis(e.clientX, e.clientY);
  });

  el.addEventListener("pointerup", (e) => {
    if (e.pointerType === "touch") return;
    if (!tracking) return;
    finish(e.clientX);
  });

  el.addEventListener("pointercancel", () => {
    tracking = false;
    axis = null;
  });
}

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

/* ─── Homepage gallery preview slider ─────────────── */
(function initHomeGallery() {
  const root = document.querySelector("[data-home-gallery]");
  if (!root) return;

  const slides = Array.from(root.querySelectorAll(".home-gallery__slide"));
  const currentEl = root.querySelector("[data-home-gallery-current]");
  const total = slides.length;
  if (total < 2) return;

  let index = 0;
  let timer = null;
  const reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function setActive(next) {
    index = ((next % total) + total) % total;
    slides.forEach((slide, i) => slide.classList.toggle("is-active", i === index));
    if (currentEl) currentEl.textContent = pad(index + 1);
  }

  function stopAuto() {
    if (timer) {
      window.clearInterval(timer);
      timer = null;
    }
  }

  function startAuto() {
    if (reduce) return;
    stopAuto();
    timer = window.setInterval(() => setActive(index + 1), 4500);
  }

  root.querySelector("[data-home-gallery-prev]")?.addEventListener("click", () => {
    setActive(index - 1);
    startAuto();
  });
  root.querySelector("[data-home-gallery-next]")?.addEventListener("click", () => {
    setActive(index + 1);
    startAuto();
  });

  bindSwipe(root.querySelector(".home-gallery__viewport"), () => {
    setActive(index - 1);
    startAuto();
  }, () => {
    setActive(index + 1);
    startAuto();
  });

  startAuto();
  document.addEventListener(
    "visibilitychange",
    () => {
      if (document.hidden) stopAuto();
      else startAuto();
    },
    { passive: true }
  );
})();

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
    // Keep the natural last frame — seeking back causes a visible jerk.
    video.pause();
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
      if (!document.hidden && video.paused && !video.ended) tryPlay();
    },
    { passive: true }
  );
})();

/* ─── Room gallery slider + lightbox ─────────────── */
(function initRoomGallery() {
  const root = document.querySelector("[data-room-gallery]");
  if (!root) return;

  const slides = Array.from(root.querySelectorAll(".room-gallery__slide"));
  const thumbs = Array.from(root.querySelectorAll("[data-gallery-thumb]"));
  const currentEl = root.querySelector("[data-gallery-current]");
  const lightbox = document.getElementById("room-lightbox");
  const lightboxImg = document.getElementById("room-lightbox-img");
  const lightboxCurrent = lightbox?.querySelector("[data-lightbox-current]");
  const total = slides.length;
  if (!total) return;

  let index = 0;
  let lightboxOpen = false;
  let lightboxScrollY = 0;

  const images = slides.map((slide) => {
    const img = slide.querySelector("img");
    return {
      src: img?.currentSrc || img?.src || "",
      alt: img?.alt || "",
    };
  });

  function setActive(next, { syncLightbox = true } = {}) {
    index = ((next % total) + total) % total;
    slides.forEach((slide, i) => slide.classList.toggle("is-active", i === index));
    thumbs.forEach((thumb, i) => {
      const on = i === index;
      thumb.classList.toggle("is-active", on);
      thumb.setAttribute("aria-selected", on ? "true" : "false");
    });
    if (currentEl) currentEl.textContent = pad(index + 1);
    if (syncLightbox && lightboxOpen) renderLightbox();
  }

  function renderLightbox() {
    if (!lightbox || !lightboxImg) return;
    const item = images[index];
    lightboxImg.src = item.src;
    lightboxImg.alt = item.alt;
    if (lightboxCurrent) lightboxCurrent.textContent = pad(index + 1);
  }

  function lockPageScroll() {
    lightboxScrollY = window.scrollY || window.pageYOffset || 0;
    document.documentElement.classList.add("lightbox-open");
    document.body.classList.add("lightbox-open");
    document.body.style.top = `-${lightboxScrollY}px`;
  }

  function unlockPageScroll() {
    const y = lightboxScrollY;
    document.documentElement.classList.remove("lightbox-open");
    document.body.classList.remove("lightbox-open");
    document.body.style.top = "";
    const html = document.documentElement;
    const prev = html.style.scrollBehavior;
    html.style.scrollBehavior = "auto";
    window.scrollTo({ top: y, left: 0, behavior: "auto" });
    html.style.scrollBehavior = prev;
  }

  function openLightbox(at) {
    if (!lightbox) return;
    // Escape any transformed ancestors (reveal animations break position:fixed).
    if (lightbox.parentElement !== document.body) {
      document.body.appendChild(lightbox);
    }
    setActive(typeof at === "number" ? at : index, { syncLightbox: false });
    renderLightbox();
    lightbox.hidden = false;
    lightbox.classList.add("is-open");
    lightbox.setAttribute("aria-hidden", "false");
    lockPageScroll();
    lightboxOpen = true;
  }

  function closeLightbox() {
    if (!lightbox) return;
    lightbox.hidden = true;
    lightbox.classList.remove("is-open");
    lightbox.setAttribute("aria-hidden", "true");
    unlockPageScroll();
    lightboxOpen = false;
  }

  root.querySelector("[data-gallery-prev]")?.addEventListener("click", () => setActive(index - 1));
  root.querySelector("[data-gallery-next]")?.addEventListener("click", () => setActive(index + 1));

  thumbs.forEach((thumb) => {
    thumb.addEventListener("click", () => setActive(Number(thumb.dataset.index) || 0));
  });

  root.querySelectorAll("[data-gallery-open]").forEach((btn) => {
    btn.addEventListener("click", () => openLightbox(Number(btn.dataset.index) || 0));
  });

  lightbox?.querySelector("[data-lightbox-close]")?.addEventListener("click", closeLightbox);
  lightbox?.querySelector("[data-lightbox-prev]")?.addEventListener("click", () => setActive(index - 1));
  lightbox?.querySelector("[data-lightbox-next]")?.addEventListener("click", () => setActive(index + 1));

  lightbox?.addEventListener("click", (e) => {
    if (e.target === lightbox) closeLightbox();
  });

  document.addEventListener("keydown", (e) => {
    if (!lightboxOpen) return;
    if (e.key === "Escape") closeLightbox();
    if (e.key === "ArrowLeft") setActive(index - 1);
    if (e.key === "ArrowRight") setActive(index + 1);
  });

  bindSwipe(root.querySelector(".room-gallery__viewport"), () => setActive(index - 1), () => setActive(index + 1));
  if (lightbox) {
    bindSwipe(lightbox, () => setActive(index - 1), () => setActive(index + 1));
  }
})();

/* ─── Site photo gallery lightbox ─────────────────── */
(function initSiteGallery() {
  const root = document.querySelector("[data-site-gallery]");
  const dataEl = document.getElementById("site-gallery-data");
  if (!root || !dataEl) return;

  let images = [];
  try {
    images = JSON.parse(dataEl.textContent || "[]");
  } catch (e) {
    images = [];
  }
  if (!Array.isArray(images) || !images.length) return;

  const lightbox = document.getElementById("site-lightbox");
  const lightboxImg = document.getElementById("site-lightbox-img");
  const lightboxCurrent = lightbox?.querySelector("[data-lightbox-current]");
  const total = images.length;
  let index = 0;
  let lightboxOpen = false;
  let lightboxScrollY = 0;

  function renderLightbox() {
    if (!lightbox || !lightboxImg) return;
    const item = images[index] || {};
    lightboxImg.src = item.url || "";
    lightboxImg.alt = item.alt || "";
    if (lightboxCurrent) lightboxCurrent.textContent = pad(index + 1);
  }

  function setActive(next) {
    index = ((next % total) + total) % total;
    if (lightboxOpen) renderLightbox();
  }

  function lockPageScroll() {
    lightboxScrollY = window.scrollY || window.pageYOffset || 0;
    document.documentElement.classList.add("lightbox-open");
    document.body.classList.add("lightbox-open");
    document.body.style.top = `-${lightboxScrollY}px`;
  }

  function unlockPageScroll() {
    const y = lightboxScrollY;
    document.documentElement.classList.remove("lightbox-open");
    document.body.classList.remove("lightbox-open");
    document.body.style.top = "";
    const html = document.documentElement;
    const prev = html.style.scrollBehavior;
    html.style.scrollBehavior = "auto";
    window.scrollTo({ top: y, left: 0, behavior: "auto" });
    html.style.scrollBehavior = prev;
  }

  function openLightbox(at) {
    if (!lightbox) return;
    if (lightbox.parentElement !== document.body) {
      document.body.appendChild(lightbox);
    }
    index = typeof at === "number" ? ((at % total) + total) % total : index;
    lightbox.hidden = false;
    lightbox.classList.add("is-open");
    lightbox.setAttribute("aria-hidden", "false");
    lightboxOpen = true;
    lockPageScroll();
    renderLightbox();
  }

  function closeLightbox() {
    if (!lightbox || !lightboxOpen) return;
    lightbox.classList.remove("is-open");
    lightbox.hidden = true;
    lightbox.setAttribute("aria-hidden", "true");
    lightboxOpen = false;
    unlockPageScroll();
  }

  root.querySelectorAll("[data-gallery-open]").forEach((btn) => {
    btn.addEventListener("click", () => openLightbox(Number(btn.dataset.index) || 0));
  });

  lightbox?.querySelector("[data-lightbox-close]")?.addEventListener("click", closeLightbox);
  lightbox?.querySelector("[data-lightbox-prev]")?.addEventListener("click", () => setActive(index - 1));
  lightbox?.querySelector("[data-lightbox-next]")?.addEventListener("click", () => setActive(index + 1));
  lightbox?.addEventListener("click", (e) => {
    if (e.target === lightbox) closeLightbox();
  });

  document.addEventListener("keydown", (e) => {
    if (!lightboxOpen) return;
    if (e.key === "Escape") closeLightbox();
    if (e.key === "ArrowLeft") setActive(index - 1);
    if (e.key === "ArrowRight") setActive(index + 1);
  });

  if (lightbox) {
    bindSwipe(lightbox, () => setActive(index - 1), () => setActive(index + 1));
  }
})();
