/**
 * Keep Bnovo booking iframe at viewport height.
 *
 * Auto-growing to full content height (via iframe-resizer / postMessage)
 * makes position:fixed lightboxes inside the module enormous
 * (.lightbox.lightbox--white), because fixed is relative to the iframe box.
 */
(function () {
  var iframe = document.getElementById("bnovo_booking_iframe");
  if (!iframe) return;

  var locking = false;

  function viewportHeight() {
    var vv = window.visualViewport;
    var viewH = vv && vv.height ? vv.height : window.innerHeight;
    return Math.round(Math.max(560, viewH - 24));
  }

  function applyHeight() {
    if (locking) return;
    locking = true;
    var height = viewportHeight();
    iframe.style.height = height + "px";
    iframe.setAttribute("height", String(height));
    window.requestAnimationFrame(function () {
      locking = false;
    });
  }

  applyHeight();
  window.addEventListener("resize", applyHeight);
  if (window.visualViewport) {
    window.visualViewport.addEventListener("resize", applyHeight);
  }

  // Bnovo may try to stretch frameElement from inside — pin it back.
  new MutationObserver(applyHeight).observe(iframe, {
    attributes: true,
    attributeFilter: ["style", "height"],
  });

  window.addEventListener("message", function (event) {
    if (!event || !event.origin) return;
    if (event.origin.indexOf("reservationsteps.ru") === -1) return;
    // Any resize signal from the module: stay viewport-sized.
    applyHeight();
  });
})();
