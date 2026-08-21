/* ─── Data ───────────────────────────────────────── */
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

const AMENITIES = [
  {
    icon: "assets/amenities/parking.png",
    title: "Охраняемая парковка",
    desc: "Большая видеонаблюдаемая парковка прямо у входа.",
  },
  {
    icon: "assets/amenities/breakfast.png",
    title: "Континентальный завтрак",
    desc: "Свежий завтрак каждое утро за 300 ₽ с персоны.",
  },
  {
    icon: "assets/amenities/taxi.png",
    title: "Такси",
    desc: "Заказ такси для гостей отеля в любое время суток.",
  },
  {
    icon: "assets/amenities/transfer.png",
    title: "Трансфер",
    desc: "Организация трансфера из аэропорта и по городу.",
  },
  {
    icon: "assets/amenities/laundry.png",
    title: "Прачечная",
    desc: "Услуги стирки и глажки для постояльцев отеля.",
  },
  {
    icon: "assets/amenities/contact.png",
    title: "Связь 24/7",
    desc: "Круглосуточная связь с администрацией по телефону и email.",
  },
  {
    icon: "assets/amenities/excursions.png",
    title: "Экскурсии",
    desc: "Организация авторских экскурсий по Казани и Татарстану.",
  },
];

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

/* ─── Modal ──────────────────────────────────────── */
const modal = document.getElementById("booking-modal");
const modalRoom = document.getElementById("modal-room");
const roomField = document.getElementById("room-field");
const roomTypeInput = document.getElementById("room-type");
const bookingForm = document.getElementById("booking-form");
const modalSuccess = document.getElementById("modal-success");

let modalClosing = false;

function openModal(roomName) {
  if (!modal || modalClosing) return;

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

  // Force reflow so transition always runs
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

  // Fallback if transitionend doesn't fire
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
  btn.addEventListener("click", () => openModal());
});

document.querySelectorAll("[data-close-modal]").forEach((el) => {
  el.addEventListener("click", closeModal);
});

document.addEventListener("keydown", (e) => {
  if (e.key === "Escape") closeModal();
});

bookingForm?.addEventListener("submit", (e) => {
  e.preventDefault();
  bookingForm.hidden = true;
  modalSuccess.hidden = false;
});

/* ─── Header / mobile nav ────────────────────────── */
const header = document.getElementById("header");
const burger = document.getElementById("burger");
const nav = document.getElementById("nav");

function setNavOpen(open) {
  nav.classList.toggle("is-open", open);
  burger.classList.toggle("is-open", open);
  burger.setAttribute("aria-expanded", String(open));
}

burger?.addEventListener("click", () => {
  setNavOpen(!nav.classList.contains("is-open"));
});

nav?.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => setNavOpen(false));
});

window.addEventListener(
  "scroll",
  () => {
    header?.classList.toggle("is-scrolled", window.scrollY > 60);
  },
  { passive: true }
);

window.addEventListener("resize", () => {
  if (window.innerWidth > 900) setNavOpen(false);
});

/* ─── Rooms ──────────────────────────────────────── */
const tabsEl = document.getElementById("room-tabs");
const roomImg = document.getElementById("room-img");
const roomTag = document.getElementById("room-tag");
const roomName = document.getElementById("room-name");
const roomSubtitle = document.getElementById("room-subtitle");
const roomPrice = document.getElementById("room-price");
const roomDesc = document.getElementById("room-desc");
const roomFeatures = document.getElementById("room-features");
const roomBook = document.getElementById("room-book");

let activeRoom = 0;

function renderRoom(index) {
  activeRoom = index;
  const room = ROOMS[index];

  tabsEl.querySelectorAll(".rooms__tab").forEach((tab, i) => {
    tab.classList.toggle("is-active", i === index);
    tab.setAttribute("aria-selected", String(i === index));
  });

  // Restart image animation
  roomImg.style.animation = "none";
  void roomImg.offsetWidth;
  roomImg.style.animation = "";
  roomImg.src = room.img;
  roomImg.alt = room.name;

  roomTag.textContent = room.tag;
  roomName.textContent = room.name;
  roomSubtitle.textContent = room.subtitle;
  roomPrice.textContent = room.price;
  roomDesc.textContent = room.desc;
  roomFeatures.innerHTML = room.features.map((f) => `<span>${f}</span>`).join("");
}

if (tabsEl) {
  ROOMS.forEach((room, i) => {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "rooms__tab" + (i === 0 ? " is-active" : "");
    btn.setAttribute("role", "tab");
    btn.setAttribute("aria-selected", String(i === 0));
    btn.textContent = room.tag;
    btn.addEventListener("click", () => renderRoom(i));
    tabsEl.appendChild(btn);
  });
  renderRoom(0);
}

roomBook?.addEventListener("click", () => {
  const room = ROOMS[activeRoom];
  openModal(`${room.name} — ${room.subtitle}`);
});

/* ─── Amenities ──────────────────────────────────── */
const amenitiesGrid = document.getElementById("amenities-grid");
if (amenitiesGrid) {
  amenitiesGrid.innerHTML = AMENITIES.map((a, i) => {
    const delay = Math.min((i % 4) * 100 + 100, 400);
    return `
      <div class="amenity reveal delay-${delay}">
        <div class="amenity__icon">
          <img src="${a.icon}" alt="" width="56" height="56" loading="lazy" />
        </div>
        <h3>${a.title}</h3>
        <p>${a.desc}</p>
      </div>
    `;
  }).join("");
}

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
