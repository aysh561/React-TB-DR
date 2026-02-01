// /phase-14-digital-referee/index.js

"use strict";

/*
  index.js
  Purpose: Phase 14 Digital Referee ka single, deterministic
  entry point aur controlled execution pipeline.
*/

const { normalizeEvidence } = require("./intake/normalizeEvidence");
const { idempotencyGuard } = require("./intake/idempotencyGuard");

const {
  mediaIntegrityCheck,
} = require("./prechecks/mediaIntegrityCheck");
const {
  matchContextCheck,
} = require("./prechecks/matchContextCheck");

const {
  antiCheatEvaluator,
} = require("./evaluation/antiCheatEvaluator");

const {
  decisionEngine,
} = require("./decision/decisionEngine");
const {
  manualOverrideGate,
} = require("./decision/manualOverrideGate");

const {
  decisionLogger,
} = require("./audit/decisionLogger");
const {
  auditTrailReader,
} = require("./audit/auditTrailReader");

/**
 * Digital Referee – Phase 14 Entry Point
 */
function runDigitalRefereePipeline({
  shotEvidence,
  verificationPacket,
  matchContext,
  adapters,
  manualOverrideRequest,
}) {
  if (
    !adapters ||
    typeof adapters.idempotencyLookup !== "function" ||
    typeof adapters.auditWriter !== "function" ||
    typeof adapters.auditReader !== "function"
  ) {
    throw new Error("REQUIRED_ADAPTER_MISSING");
  }

  // 1. Normalize Evidence
  const normalizedEvidence = normalizeEvidence({
    shotEvidence,
    verificationPacket,
    matchContext,
  });

  // 2. Idempotency Guard
  const idempotencyResult = idempotencyGuard(
    normalizedEvidence.fingerprint,
    adapters.idempotencyLookup
  );

  if (idempotencyResult.isDuplicate) {
    const previous = auditTrailReader(
      { decisionId: idempotencyResult.existingRef },
      adapters.auditReader
    );

    const record = previous.records[0];
    return {
      verificationStatus: record.verificationStatus,
      reason: record.reason,
      decisionId: record.decisionId,
      loggedAt: record.loggedAt,
    };
  }

  // 3. Prechecks
  const mediaIntegrity = mediaIntegrityCheck(normalizedEvidence);
  const matchContextResult =
    matchContextCheck(normalizedEvidence);

  const precheckResults = {
    mediaIntegrity,
    matchContext: matchContextResult,
  };

  // 4. Anti-Cheat Evaluation
  const antiCheatResult = antiCheatEvaluator(
    normalizedEvidence,
    precheckResults
  );

  // 5. Decision Engine
  const decision = decisionEngine(
    precheckResults,
    antiCheatResult
  );

  // 6. Manual Override Gate (no execution)
  let supersedesDecisionId = null;
  if (manualOverrideRequest) {
    const gateResult = manualOverrideGate(
      decision,
      manualOverrideRequest
    );
    if (gateResult.allowed === true) {
      supersedesDecisionId =
        manualOverrideRequest.supersedesDecisionId ?? null;
    }
  }

  // 7. Decision Logging (append-only)
  const auditResult = decisionLogger(
    decision,
    {
      fingerprint: normalizedEvidence.fingerprint,
      supersedesDecisionId,
    },
    adapters.auditWriter
  );

  // 8. Final Output
  return {
    verificationStatus: decision.verificationStatus,
    reason: decision.reason,
    decisionId: auditResult.decisionId,
    loggedAt: auditResult.loggedAt,
  };
}

module.exports = {
  runDigitalRefereePipeline,
};
