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
  var contentHeight = 4800;
  var scrollY = 0;
  var INITIAL_HEIGHT = 4800;
  var acceptHeight = false;

  var mobileQuery = window.matchMedia("(max-width: 900px)");

  function isMobile() {
    return mobileQuery.matches;
  }

  if (isMobile()) {
    iframe.style.minHeight = "0";
    iframe.setAttribute("scrolling", "auto");
  } else {
    iframe.style.height = INITIAL_HEIGHT + "px";
    iframe.setAttribute("height", String(INITIAL_HEIGHT));
  }

  function viewportHeight() {
    var vv = window.visualViewport;
    var viewH = vv && vv.height ? vv.height : window.innerHeight;
    return Math.round(Math.max(480, viewH));
  }

  function setIframeHeight(h) {
    if (isMobile()) return;
    if (!overlayMode && !acceptHeight) return;
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

  function pageInfo(windowH) {
    var rect = iframe.getBoundingClientRect();
    var body = document.body.getBoundingClientRect();
    var y = window.scrollY || window.pageYOffset || 0;
    var viewH = windowH || viewportHeight();
    return {
      scrollTop: y,
      offsetTop: rect.top + y,
      headerHeight: 0,
      headMargin: 0,
      windowHeight: viewH,
      clientWidth: document.documentElement.clientWidth || window.innerWidth,
      clientHeight: viewH,
      footerHeight: 0,
      bodyPosition: {
        top: body.top,
        left: body.left,
        width: body.width,
        height: body.height,
      },
      iFramePosition: {
        top: rect.top,
        left: rect.left,
        width: rect.width,
        height: rect.height,
      },
    };
  }

  function sendBnovo(payload) {
    if (!iframe.iFrameResizer || typeof iframe.iFrameResizer.sendMessage !== "function") {
      return false;
    }
    iframe.iFrameResizer.sendMessage(payload);
    return true;
  }

  // While the iframe is pinned to the screen, tell Bnovo the module IS the viewport.
  // Otherwise it places popups using the full stretched height and they land off-screen.
  function visibleFrameInfo() {
    var rect = iframe.getBoundingClientRect();
    var viewH = Math.round(rect.height || viewportHeight());
    var viewW = Math.round(rect.width || window.innerWidth);
    return {
      scrollTop: 0,
      offsetTop: 0,
      headerHeight: 0,
      headMargin: 0,
      windowHeight: viewH,
      clientWidth: viewW,
      clientHeight: viewH,
      footerHeight: 0,
      bodyPosition: { top: 0, left: 0, width: viewW, height: viewH },
      iFramePosition: { top: 0, left: 0, width: viewW, height: viewH },
    };
  }

  function syncBnovoFrame() {
    if (!didInit) return;
    sendBnovo({ value: overlayMode ? visibleFrameInfo() : pageInfo() });
  }

  var didInit = false;
  function revealRooms() {
    if (!didInit) {
      // A tall first report makes Bnovo init every room slider. On a phone the
      // lasting report must be the real screen, or booking popups drop to the
      // bottom of the stretched module.
      var boot = pageInfo(viewportHeight());
      if (!sendBnovo({ type: "init", height: "fixed", value: boot })) return;
      didInit = true;
      return;
    }
    syncBnovoFrame();
  }

  function enterOverlayMode() {
    if (isMobile() || overlayMode) return;
    overlayMode = true;
    scrollY = window.scrollY || window.pageYOffset || 0;

    document.documentElement.classList.add("bnovo-overlay-open");
    document.body.classList.add("bnovo-overlay-open");
    document.body.style.top = "-" + scrollY + "px";

    iframe.classList.add("booking-page__iframe--overlay");
    setIframeHeight(viewportHeight());
    window.requestAnimationFrame(syncBnovoFrame);
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
    if (isMobile()) window.requestAnimationFrame(syncBnovoFrame);
  }

  var backBtn = document.getElementById("booking-back-btn");

  if (backBtn) {
    backBtn.addEventListener("click", function (event) {
      var target = backBtn.getAttribute("href");
      if (!target) return;
      event.preventDefault();
      var next = new URL(target, window.location.href);
      var now = new URL(window.location.href);
      var samePath = now.pathname.replace(/\/+$/, "") === next.pathname.replace(/\/+$/, "");
      if (now.origin === next.origin && samePath && now.search === next.search) {
        window.location.reload();
        return;
      }
      window.location.assign(next.href);
    });
  }

  // Bnovo skips the «Размещение» summary when the iframe is 640px or narrower.
  // Lay the module out wider than that and zoom it back to the phone width,
  // so the summary opens and stays inside the visible module.
  var MOBILE_LAYOUT_WIDTH = 720;

  function fitMobileFrame() {
    if (!isMobile()) {
      iframe.style.zoom = "";
      iframe.style.removeProperty("width");
      iframe.style.removeProperty("height");
      return;
    }

    var frame = iframe.parentElement;
    var availW = frame ? frame.clientWidth : 0;
    var availH = frame ? frame.clientHeight : 0;
    if (!availW || !availH) return;

    iframe.style.minHeight = "0";
    iframe.setAttribute("scrolling", "auto");

    if (availW > 640) {
      iframe.style.zoom = "";
      iframe.style.setProperty("width", "100%", "important");
      iframe.style.setProperty("height", "100%", "important");
      return;
    }

    var zoom = availW / MOBILE_LAYOUT_WIDTH;
    iframe.style.setProperty("width", MOBILE_LAYOUT_WIDTH + "px", "important");
    iframe.style.setProperty("height", Math.ceil(availH / zoom) + "px", "important");
    iframe.style.setProperty("zoom", String(zoom));
  }

  function startResizer() {
    if (isMobile()) return;
    acceptHeight = true;
    if (typeof window.iFrameResize !== "function") return;
    window.iFrameResize(
      {
        log: false,
        checkOrigin: false,
        heightCalculationMethod: "lowestElement",
        tolerance: 12,
        minHeight: 640,
        onReady: function () {
          revealRooms();
          window.setTimeout(revealRooms, 400);
          window.setTimeout(revealRooms, 1600);
        },
        onResized: function (data) {
          if (overlayMode) return;
          if (data && data.height) setIframeHeight(data.height);
        },
      },
      iframe
    );
  }

  // Keep the iframe tall until Bnovo inits sliders/images for every room.
  // Their lazy-loader only touches rooms inside the iframe viewport.
  if (document.readyState === "complete") {
    window.setTimeout(startResizer, 2200);
  } else {
    window.addEventListener("load", function () {
      window.setTimeout(startResizer, 2200);
    });
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
    fitMobileFrame();
    if (overlayMode) setIframeHeight(viewportHeight());
    if (isMobile()) syncBnovoFrame();
  });
  if (window.visualViewport) {
    window.visualViewport.addEventListener("resize", function () {
      fitMobileFrame();
      if (overlayMode) setIframeHeight(viewportHeight());
      if (isMobile()) syncBnovoFrame();
    });
  }

  var frameSyncQueued = false;
  function queueFrameSync() {
    if (!isMobile() || frameSyncQueued) return;
    frameSyncQueued = true;
    window.requestAnimationFrame(function () {
      frameSyncQueued = false;
      syncBnovoFrame();
    });
  }

  fitMobileFrame();
  window.addEventListener("load", fitMobileFrame);
  window.requestAnimationFrame(fitMobileFrame);

  window.addEventListener("scroll", queueFrameSync, { passive: true });
  window.addEventListener("pointerdown", queueFrameSync, { passive: true });
})();
