const timestamp = new Date().toISOString();

const latency =
    typeof performance !== "undefined" && typeof performance.now === "function"
        ? performance.now()
        : null;

const captureDelay = null;

let uiFreeze = null;
let now = null;

if (
    typeof performance !== "undefined" &&
    typeof performance.now === "function"
) {
    now = performance.now();
    if (
        typeof window !== "undefined" &&
        window.__ref_lastNow &&
        now - window.__ref_lastNow > 50
    ) {
        uiFreeze = now - window.__ref_lastNow;
    }
    if (typeof window !== "undefined") {
        window.__ref_lastNow = now;
    }
}

const inputLag =
    typeof performance !== "undefined" && performance.eventCounts
        ? performance.eventCounts.size
        : null;

const visibilityState =
    typeof document !== "undefined" && document.visibilityState
        ? document.visibilityState
        : null;

return {
    timestamp,
    latency,
    captureDelay,
    uiFreeze,
    inputLag,
    visibilityState
};
