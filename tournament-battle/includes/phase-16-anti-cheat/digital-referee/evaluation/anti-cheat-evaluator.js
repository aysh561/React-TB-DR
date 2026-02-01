// /phase-14-digital-referee/evaluation/antiCheatEvaluator.js

"use strict";

/*
  antiCheatEvaluator.js
  Purpose: Normalized evidence aur precheck outputs par
  deterministic, non-punitive anti-cheat signals generate karna.
*/

/**
 * Anti-Cheat Evaluator
 *
 * @param {Object} normalizedEvidence
 * @param {Object} precheckResults
 * @returns {{ flags: string[], confidence: "low" | "medium" | "high" }}
 */
function antiCheatEvaluator(normalizedEvidence, precheckResults) {
  const flags = [];

  const mediaIntegrity = precheckResults?.mediaIntegrity;
  const matchContext = precheckResults?.matchContext;

  // Precheck outcome based signals
  if (mediaIntegrity && mediaIntegrity.passed === false) {
    flags.push("MEDIA_INTEGRITY_WEAK");
  }

  if (matchContext && matchContext.passed === false) {
    flags.push("MATCH_CONTEXT_WEAK");
  }

  // Metadata-based deterministic signals
  const shotEvidence =
    normalizedEvidence?.normalizedFields?.shotEvidence;

  if (shotEvidence?.metadata) {
    const { size, type, format } = shotEvidence.metadata;

    if (typeof size === "number" && size < 1024) {
      flags.push("MEDIA_SIZE_UNUSUALLY_SMALL");
    }

    if (typeof type === "string" && type.trim() === "") {
      flags.push("MEDIA_TYPE_EMPTY");
    }

    if (typeof format === "string" && format.trim() === "") {
      flags.push("MEDIA_FORMAT_EMPTY");
    }
  }

  // Confidence derivation (deterministic)
  let confidence = "high";

  if (flags.length >= 3) {
    confidence = "low";
  } else if (flags.length > 0) {
    confidence = "medium";
  }

  return {
    flags,
    confidence,
  };
}

module.exports = {
  antiCheatEvaluator,
};
