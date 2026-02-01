// /phase-14-digital-referee/audit/decisionLogger.js

"use strict";

/*
  decisionLogger.js
  Purpose: Verification decisions ka immutable,
  append-only audit record generate aur emit karna.
*/

const crypto = require("crypto");

/**
 * Deep freeze utility — immutability guarantee
 */
function deepFreeze(obj) {
  if (obj && typeof obj === "object" && !Object.isFrozen(obj)) {
    Object.freeze(obj);
    Object.getOwnPropertyNames(obj).forEach((prop) => {
      if (
        obj[prop] &&
        typeof obj[prop] === "object" &&
        !Object.isFrozen(obj[prop])
      ) {
        deepFreeze(obj[prop]);
      }
    });
  }
  return obj;
}

/**
 * Deterministic decisionId generator
 * (runtime fields excluded)
 */
function generateDecisionId(input) {
  const serialized = JSON.stringify(input);
  return crypto.createHash("sha256").update(serialized).digest("hex");
}

/**
 * Decision Logger
 *
 * @param {{ verificationStatus: string, reason: string | null }} decision
 * @param {{ fingerprint: string, decisionId?: string, supersedesDecisionId?: string }} context
 * @param {Function} writer - append-only persistence adapter
 * @returns {{ decisionId: string, loggedAt: string }}
 */
function decisionLogger(decision, context, writer) {
  const baseRecord = {
    fingerprint: context.fingerprint,
    verificationStatus: decision.verificationStatus,
    reason: decision.reason ?? null,
    supersedesDecisionId: context.supersedesDecisionId ?? null,
  };

  const decisionId =
    context.decisionId ?? generateDecisionId(baseRecord);

  const loggedAt = new Date().toISOString();

  const logRecord = {
    ...baseRecord,
    decisionId,
    loggedAt,
  };

  deepFreeze(logRecord);

  if (typeof writer === "function") {
    writer(logRecord);
  }

  return {
    decisionId,
    loggedAt,
  };
}

module.exports = {
  decisionLogger,
};
