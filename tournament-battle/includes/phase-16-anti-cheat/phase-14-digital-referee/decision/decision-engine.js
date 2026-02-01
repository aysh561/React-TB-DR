// /phase-14-digital-referee/decision/decisionEngine.js

"use strict";

/*
  decisionEngine.js
  Purpose: Prechecks aur anti-cheat signals ko
  deterministic tareeqe se merge kar ke
  single authoritative verification decision generate karna.
*/

/**
 * Decision Engine
 *
 * @param {Object} precheckResults
 * @param {{ flags: string[], confidence: "low" | "medium" | "high" }} antiCheatResult
 * @returns {{ verificationStatus: "approved" | "rejected" | "pending", reason: string | null }}
 */
function decisionEngine(precheckResults, antiCheatResult) {
  const mediaIntegrity = precheckResults?.mediaIntegrity;
  const matchContext = precheckResults?.matchContext;

  // Hard reject: media integrity failed
  if (mediaIntegrity && mediaIntegrity.passed === false) {
    return {
      verificationStatus: "rejected",
      reason: "MEDIA_INTEGRITY_FAILED",
    };
  }

  // Hard reject: match context failed
  if (matchContext && matchContext.passed === false) {
    return {
      verificationStatus: "rejected",
      reason: "MATCH_CONTEXT_FAILED",
    };
  }

  // Pending: low confidence anti-cheat
  if (antiCheatResult && antiCheatResult.confidence === "low") {
    return {
      verificationStatus: "pending",
      reason: "LOW_ANTI_CHEAT_CONFIDENCE",
    };
  }

  // Approve: no hard reject and confidence medium/high
  return {
    verificationStatus: "approved",
    reason: null,
  };
}

module.exports = {
  decisionEngine,
};
