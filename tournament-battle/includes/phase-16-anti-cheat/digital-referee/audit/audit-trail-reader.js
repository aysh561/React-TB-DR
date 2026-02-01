// /phase-14-digital-referee/audit/auditTrailReader.js

"use strict";

/*
  auditTrailReader.js
  Purpose: Audit trail ko read-only aur deterministic tareeqe se
  expose karna for forensic, debug aur compliance review.
*/

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
 * Audit Trail Reader
 *
 * @param {{ fingerprint?: string, decisionId?: string }} query
 * @param {Function} reader - read-only persistence adapter
 * @returns {{
 *   records: Array<{
 *     decisionId: string,
 *     fingerprint: string,
 *     verificationStatus: string,
 *     reason: string | null,
 *     supersedesDecisionId: string | null,
 *     loggedAt: string
 *   }>
 * }}
 */
function auditTrailReader(query, reader) {
  const safeQuery = query || {};

  let records = [];
  if (typeof reader === "function") {
    records = reader(safeQuery) || [];
  }

  // Deterministic ordering: loggedAt ASC, then decisionId ASC
  const ordered = records.slice().sort((a, b) => {
    if (a.loggedAt < b.loggedAt) return -1;
    if (a.loggedAt > b.loggedAt) return 1;
    if (a.decisionId < b.decisionId) return -1;
    if (a.decisionId > b.decisionId) return 1;
    return 0;
  });

  const normalized = ordered.map((r) =>
    deepFreeze({
      decisionId: r.decisionId,
      fingerprint: r.fingerprint,
      verificationStatus: r.verificationStatus,
      reason: r.reason ?? null,
      supersedesDecisionId: r.supersedesDecisionId ?? null,
      loggedAt: r.loggedAt,
    })
  );

  return {
    records: normalized,
  };
}

module.exports = {
  auditTrailReader,
};
