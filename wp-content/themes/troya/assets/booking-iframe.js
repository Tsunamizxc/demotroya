/**
 * Bnovo booking iframe: grow with content (parent-page scroll),
 * but pin to the viewport while their lightbox/modals are open.
 *
 * Fixed full-content height makes .lightbox.lightbox--white enormous;
 * a permanently viewport-locked iframe breaks room list scroll on mobile.
 */
(function () {
  var iframe = document.getElementById("bnovo_booking_iframe");
  if (!iframe) return;

  var locking = false;
  var overlayMode = false;
  var contentHeight = 800;
  var scrollY = 0;

  function viewportHeight() {
    var vv = window.visualViewport;
    var viewH = vv && vv.height ? vv.height : window.innerHeight;
    return Math.round(Math.max(480, viewH));
  }

  function setIframeHeight(h) {
    if (locking) return;
    locking = true;
    var height = Math.ceil(Number(h));
    if (!height || height < 400) {
      locking = false;
      return;
    }
    if (!overlayMode) {
      height = Math.min(height, 12000);
      contentHeight = height;
    }
    iframe.style.height = height + "px";
    iframe.setAttribute("height", String(height));
    window.requestAnimationFrame(function () {
      locking = false;
    });
  }

  function enterOverlayMode() {
    if (overlayMode) return;
    overlayMode = true;
    scrollY = window.scrollY || window.pageYOffset || 0;

    document.documentElement.classList.add("bnovo-overlay-open");
    document.body.classList.add("bnovo-overlay-open");
    document.body.style.top = "-" + scrollY + "px";

    iframe.classList.add("booking-page__iframe--overlay");
    setIframeHeight(viewportHeight());
  }

  function exitOverlayMode() {
    if (!overlayMode) return;
    overlayMode = false;

    iframe.classList.remove("booking-page__iframe--overlay");
    document.documentElement.classList.remove("bnovo-overlay-open");
    document.body.classList.remove("bnovo-overlay-open");
    document.body.style.top = "";
    window.scrollTo(0, scrollY);

    setIframeHeight(contentHeight);
  }

  if (typeof window.iFrameResize === "function") {
    window.iFrameResize(
      {
        log: false,
        checkOrigin: false,
        heightCalculationMethod: "lowestElement",
        tolerance: 12,
        minHeight: 640,
        onResized: function (data) {
          if (overlayMode) return;
          if (data && data.height) setIframeHeight(data.height);
        },
      },
      iframe
    );
  }

  window.addEventListener("message", function (event) {
    if (!event || !event.origin) return;
    if (event.origin.indexOf("reservationsteps.ru") === -1) return;

    var data = event.data;

    if (typeof data === "number") {
      if (!overlayMode) setIframeHeight(data);
      return;
    }
    if (typeof data === "string" && /^\d+$/.test(data)) {
      if (!overlayMode) setIframeHeight(data);
      return;
    }
    if (!data || typeof data !== "object") return;

    if (!overlayMode) {
      if (data.height) setIframeHeight(data.height);
      if (data.params && data.params.height) setIframeHeight(data.params.height);
    }

    if (data.event !== "bnovobook_signal" || !data.params) return;

    var name = data.params.name;
    if (name === "block_scrolls") {
      enterOverlayMode();
      return;
    }
    if (name === "allow_scrolls") {
      exitOverlayMode();
      return;
    }
    if (name === "container_scroll_to" && !overlayMode) {
      var top = Number(data.params.scrollTop);
      if (!isNaN(top)) {
        var offset = iframe.getBoundingClientRect().top + (window.scrollY || 0);
        window.scrollTo(0, Math.max(0, offset + top));
      }
    }
  });

  window.addEventListener("resize", function () {
    if (overlayMode) setIframeHeight(viewportHeight());
  });
  if (window.visualViewport) {
    window.visualViewport.addEventListener("resize", function () {
      if (overlayMode) setIframeHeight(viewportHeight());
    });
  }
})();
