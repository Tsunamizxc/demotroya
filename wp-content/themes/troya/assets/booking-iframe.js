/**
 * Auto-fit Bnovo booking iframe height (iframe-resizer + fallback).
 */
(function () {
  var iframe = document.getElementById("bnovo_booking_iframe");
  if (!iframe) return;

  function applyHeight(h) {
    var height = Math.ceil(Number(h));
    if (!height || height < 400) return;
    // Cap runaway values but allow tall booking flows.
    height = Math.min(height, 12000);
    iframe.style.height = height + "px";
    iframe.setAttribute("height", String(height));
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
          if (data && data.height) applyHeight(data.height);
        },
      },
      iframe
    );
  }

  // Fallback: some Bnovo builds also emit numeric / signal heights.
  window.addEventListener("message", function (event) {
    if (!event || !event.origin) return;
    if (event.origin.indexOf("reservationsteps.ru") === -1) return;

    var data = event.data;
    if (typeof data === "number") {
      applyHeight(data);
      return;
    }
    if (typeof data === "string" && /^\d+$/.test(data)) {
      applyHeight(data);
      return;
    }
    if (data && typeof data === "object") {
      if (data.height) applyHeight(data.height);
      if (data.params && data.params.height) applyHeight(data.params.height);
    }
  });
})();
