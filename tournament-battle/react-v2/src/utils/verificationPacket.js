// Generates verification packet for Digital Referee System Phase 9
export function generateVerificationPacket(hash) {
    const timestamp = new Date().toISOString();
    const userAgent = typeof navigator !== "undefined" && navigator.userAgent ? navigator.userAgent : null;
    const screenWidth = typeof window !== "undefined" && window.screen && window.screen.width ? window.screen.width : null;
    const screenHeight = typeof window !== "undefined" && window.screen && window.screen.height ? window.screen.height : null;
    const deviceMemory = typeof navigator !== "undefined" && typeof navigator.deviceMemory !== "undefined" ? navigator.deviceMemory : null;
    const hardwareConcurrency = typeof navigator !== "undefined" && typeof navigator.hardwareConcurrency !== "undefined" ? navigator.hardwareConcurrency : null;

    return {
        hash,
        timestamp,
        userAgent,
        screenWidth,
        screenHeight,
        deviceMemory,
        hardwareConcurrency
    };
}
