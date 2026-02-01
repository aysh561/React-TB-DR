// /phase-14-digital-referee/intake/normalizeEvidence.js

"use strict";

/*
  normalizeEvidence.js
  Purpose: Raw inputs ko deterministic, immutable, audit-safe
  NormalizedEvidenceRecord me convert karna.
  Is file me koi decision ya status assign nahi hota.
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
 * Deterministic stringify — stable field order
 * Arrays handling:
 * - Order preserved as-is
 * - Har element recursively stableStringify hota hai
 * - Is behavior ko canonical maana gaya hai
 */
function stableStringify(value) {
  if (value === null || typeof value !== "object") {
    return JSON.stringify(value);
  }

  if (Array.isArray(value)) {
    return (
      "[" +
      value
        .map((item) => stableStringify(item))
        .join(",") +
      "]"
    );
  }

  const keys = Object.keys(value).sort();
  return (
    "{" +
    keys
      .map(
        (key) =>
          JSON.stringify(key) + ":" + stableStringify(value[key])
      )
      .join(",") +
    "}"
  );
}

/**
 * Fingerprint generator — same input => same hash
 */
function generateFingerprint(canonicalObject) {
  const serialized = stableStringify(canonicalObject);
  return crypto.createHash("sha256").update(serialized).digest("hex");
}

/**
 * Structural sanity check (non-decisive)
 */
function collectIntakeFlags({ shotEvidence, verificationPacket, matchContext }) {
  const flags = [];

  if (!shotEvidence || typeof shotEvidence !== "object") {
    flags.push("MISSING_OR_INVALID_SHOT_EVIDENCE");
  }

  if (!verificationPacket || typeof verificationPacket !== "object") {
    flags.push("MISSING_OR_INVALID_VERIFICATION_PACKET");
  }

  if (!matchContext || typeof matchContext !== "object") {
    flags.push("MISSING_OR_INVALID_MATCH_CONTEXT");
  }

  return flags;
}

/**
 * Main Normalizer
 */
function normalizeEvidence({
  shotEvidence,
  verificationPacket,
  matchContext,
}) {
  const intakeFlags = collectIntakeFlags({
    shotEvidence,
    verificationPacket,
    matchContext,
  });

  const normalizedFields = {
    shotEvidence: {
      mediaRef: shotEvidence?.mediaRef ?? null,
      metadata: shotEvidence?.metadata ?? null,
      capturedAt: shotEvidence?.capturedAt ?? null,
    },

    verificationPacket: {
      packetId: verificationPacket?.packetId ?? null,
      generatedAt: verificationPacket?.generatedAt ?? null,
      payload: verificationPacket?.payload ?? null,
      checksum: verificationPacket?.checksum ?? null,
    },

    matchContext: {
      tournamentId: matchContext?.tournamentId ?? null,
      matchId: matchContext?.matchId ?? null,
      roundId: matchContext?.roundId ?? null,
      playerAId: matchContext?.playerAId ?? null,
      playerBId: matchContext?.playerBId ?? null,
    },
  };

  const fingerprint = generateFingerprint(normalizedFields);

  const normalizedRecord = {
    fingerprint,
    normalizedFields,
    intakeFlags,
    sourceRefs: {
      shotEvidenceRef: shotEvidence?.mediaRef ?? null,
      verificationPacketId: verificationPacket?.packetId ?? null,
    },
  };

  return deepFreeze(normalizedRecord);
}

module.exports = {
  normalizeEvidence,
};
