// /phase-14-digital-referee/decision/manualOverrideGate.js

"use strict";

/*
  manualOverrideGate.js
  Purpose: Manual referee override ko strictly gate karna
  bina koi decision execute ya state mutate kiye.
*/

/**
 * Manual Override Gate
 *
 * @param {{ verificationStatus: "approved" | "rejected" | "pending", reason: string | null }} currentDecision
 * @param {{ requestedStatus: "approved" | "rejected" | "pending", reason: string }} overrideRequest
 * @returns {{ allowed: boolean, reason: string | null }}
 */
function manualOverrideGate(currentDecision, overrideRequest) {
  if (
    !currentDecision ||
    currentDecision.verificationStatus !== "pending"
  ) {
    return {
      allowed: false,
      reason: "OVERRIDE_NOT_ALLOWED_FOR_CURRENT_STATE",
    };
  }

  const { requestedStatus, reason } = overrideRequest || {};

  if (
    requestedStatus !== "approved" &&
    requestedStatus !== "rejected"
  ) {
    return {
      allowed: false,
      reason: "REQUESTED_STATUS_INVALID",
    };
  }

  if (
    typeof reason !== "string" ||
    reason.trim().length === 0
  ) {
    return {
      allowed: false,
      reason: "OVERRIDE_REASON_REQUIRED",
    };
  }

  return {
    allowed: true,
    reason: null,
  };
}

module.exports = {
  manualOverrideGate,
};
