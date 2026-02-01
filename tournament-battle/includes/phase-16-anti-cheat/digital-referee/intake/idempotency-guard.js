// /phase-14-digital-referee/intake/idempotencyGuard.js

"use strict";

/*
  idempotencyGuard.js
  Purpose: Duplicate submissions ko fingerprint ke basis par
  deterministic aur side-effect-free tareeqe se gate karna.
*/

/**
 * Idempotency Guard
 *
 * @param {string} fingerprint
 * @param {Function} existingRecordsLookup - read-only lookup adapter
 * @returns {{ isDuplicate: boolean, existingRef: string | null }}
 */
function idempotencyGuard(fingerprint, existingRecordsLookup) {
  let existingRef = null;

  if (typeof existingRecordsLookup === "function") {
    existingRef = existingRecordsLookup(fingerprint) ?? null;
  }

  if (existingRef) {
    return {
      isDuplicate: true,
      existingRef: existingRef,
    };
  }

  return {
    isDuplicate: false,
    existingRef: null,
  };
}

module.exports = {
  idempotencyGuard,
};
