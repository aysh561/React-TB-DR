<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Phase 18 — Global Tournament Infrastructure
| FILE 25 / 27 — Governance Contracts (Read-Only)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Governance Policy Contract
|--------------------------------------------------------------------------
| Required keys:
| - policy_id (string)
| - scope (string)
| - status (bool)
|
*/

define('TB_PHASE18_GOVERNANCE_POLICY_KEYS', [
    'policy_id',
    'scope',
    'status',
]);

/*
|--------------------------------------------------------------------------
| Governance Metadata Contract
|--------------------------------------------------------------------------
| Required keys:
| - version (string)
| - issued_at (int)
| - source (string)
|
*/

define('TB_PHASE18_GOVERNANCE_METADATA_KEYS', [
    'version',
    'issued_at',
    'source',
]);
