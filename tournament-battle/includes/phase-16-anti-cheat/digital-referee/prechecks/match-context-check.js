// /phase-14-digital-referee/prechecks/matchContextCheck.js

"use strict";

/*
  matchContextCheck.js
  Purpose: Normalized match context par
  cheap, deterministic structural + sanity precheck.
*/

/**
 * Match Context Precheck
 *
 * @param {Object} normalizedEvidence
 * @returns {{ passed: boolean, reason: string | null }}
 */
function matchContextCheck(normalizedEvidence) {
  const matchContext =
    normalizedEvidence?.normalizedFields?.matchContext;

  if (!matchContext || typeof matchContext !== "object") {
    return {
      passed: false,
      reason: "MATCH_CONTEXT_MISSING",
    };
  }

  const {
    tournamentId,
    matchId,
    roundId,
    playerAId,
    playerBId,
  } = matchContext;

  if (
    typeof tournamentId !== "string" ||
    tournamentId.trim().length === 0
  ) {
    return {
      passed: false,
      reason: "TOURNAMENT_ID_MISSING_OR_INVALID",
    };
  }

  if (
    typeof matchId !== "string" ||
    matchId.trim().length === 0
  ) {
    return {
      passed: false,
      reason: "MATCH_ID_MISSING_OR_INVALID",
    };
  }

  if (
    typeof playerAId !== "string" ||
    playerAId.trim().length === 0
  ) {
    return {
      passed: false,
      reason: "PLAYER_A_ID_MISSING_OR_INVALID",
    };
  }

  if (
    typeof playerBId !== "string" ||
    playerBId.trim().length === 0
  ) {
    return {
      passed: false,
      reason: "PLAYER_B_ID_MISSING_OR_INVALID",
    };
  }

  if (playerAId === playerBId) {
    return {
      passed: false,
      reason: "PLAYER_IDS_CANNOT_BE_EQUAL",
    };
  }

  if (
    roundId !== null &&
    typeof roundId !== "string"
  ) {
    return {
      passed: false,
      reason: "ROUND_ID_INVALID",
    };
  }

  return {
    passed: true,
    reason: null,
  };
}

module.exports = {
  matchContextCheck,
};
