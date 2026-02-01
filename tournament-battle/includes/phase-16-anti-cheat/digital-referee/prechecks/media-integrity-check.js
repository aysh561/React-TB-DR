// /phase-14-digital-referee/prechecks/mediaIntegrityCheck.js

"use strict";

/*
  mediaIntegrityCheck.js
  Purpose: Shot evidence ke media metadata par
  cheap, deterministic integrity precheck.
*/

/**
 * Media Integrity Precheck
 *
 * @param {Object} normalizedEvidence
 * @returns {{ passed: boolean, reason: string | null }}
 */
function mediaIntegrityCheck(normalizedEvidence) {
  const shotEvidence = normalizedEvidence?.normalizedFields?.shotEvidence;

  if (!shotEvidence || typeof shotEvidence !== "object") {
    return {
      passed: false,
      reason: "SHOT_EVIDENCE_MISSING",
    };
  }

  const { mediaRef, metadata } = shotEvidence;

  if (!mediaRef || typeof mediaRef !== "string") {
    return {
      passed: false,
      reason: "MEDIA_REF_MISSING_OR_INVALID",
    };
  }

  if (!metadata || typeof metadata !== "object") {
    return {
      passed: false,
      reason: "MEDIA_METADATA_MISSING",
    };
  }

  if (!metadata.type || typeof metadata.type !== "string") {
    return {
      passed: false,
      reason: "MEDIA_TYPE_MISSING_OR_INVALID",
    };
  }

  if (
    metadata.size == null ||
    typeof metadata.size !== "number" ||
    metadata.size <= 0
  ) {
    return {
      passed: false,
      reason: "MEDIA_SIZE_INVALID",
    };
  }

  if (!metadata.format || typeof metadata.format !== "string") {
    return {
      passed: false,
      reason: "MEDIA_FORMAT_MISSING_OR_INVALID",
    };
  }

  return {
    passed: true,
    reason: null,
  };
}

module.exports = {
  mediaIntegrityCheck,
};
