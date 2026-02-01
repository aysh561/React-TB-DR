<?php
declare(strict_types=1);

namespace TB\Phase18\Contracts;

/*
|--------------------------------------------------------------------------
| Region Contracts Versioning (Optional)
|--------------------------------------------------------------------------
*/

if (\defined('TB_PHASE18_REGION_CONTRACT_VERSION')) {
    throw new \RuntimeException(
        'Phase 18 hard fail: TB_PHASE18_REGION_CONTRACT_VERSION already defined'
    );
}

\define('TB_PHASE18_REGION_CONTRACT_VERSION', '1.0.0');

/*
|--------------------------------------------------------------------------
| Region Identity Contract
|--------------------------------------------------------------------------
|
| Required keys:
| - region_id (string)
| - region_code (string)
| - region_name (string)
|
*/

\define('TB_PHASE18_REGION_IDENTITY_KEYS', [
    'region_id',
    'region_code',
    'region_name',
]);

/*
|--------------------------------------------------------------------------
| Region Routing Metadata Contract
|--------------------------------------------------------------------------
|
| Required keys:
| - routing_id (string)
| - status (string)
| - is_available (bool)
|
*/

\define('TB_PHASE18_REGION_ROUTING_KEYS', [
    'routing_id',
    'status',
    'is_available',
]);

/*
|--------------------------------------------------------------------------
| Region Capability Flags (Read-only)
|--------------------------------------------------------------------------
*/

\define('TB_PHASE18_REGION_CAPABILITY_REALTIME', true);
\define('TB_PHASE18_REGION_CAPABILITY_PAYMENTS', true);
\define('TB_PHASE18_REGION_CAPABILITY_SPECTATOR', true);
\define('TB_PHASE18_REGION_CAPABILITY_AI_COACHING', true);
