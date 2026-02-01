<?php
declare(strict_types=1);

namespace TB\Phase18\Contracts;

/*
|--------------------------------------------------------------------------
| Global API Versioning
|--------------------------------------------------------------------------
*/

if (\defined('TB_PHASE18_API_VERSION')) {
    throw new \RuntimeException(
        'Phase 18 hard fail: TB_PHASE18_API_VERSION already defined'
    );
}

\define('TB_PHASE18_API_VERSION', '1.0.0');

if (\defined('TB_PHASE18_API_COMPAT')) {
    throw new \RuntimeException(
        'Phase 18 hard fail: TB_PHASE18_API_COMPAT already defined'
    );
}

\define('TB_PHASE18_API_COMPAT', true);

/*
|--------------------------------------------------------------------------
| Tournament Payload Contract (Shape Definition)
|--------------------------------------------------------------------------
|
| Required keys:
| - tournament_id (string)
| - region_id (string)
| - state (string)
| - players (array)
| - created_at (int)
| - updated_at (int)
|
*/

\define('TB_PHASE18_TOURNAMENT_PAYLOAD_KEYS', [
    'tournament_id',
    'region_id',
    'state',
    'players',
    'created_at',
    'updated_at',
]);

/*
|--------------------------------------------------------------------------
| Player Identifier Contract
|--------------------------------------------------------------------------
|
| Required keys:
| - player_id (string)
| - identity_id (string)
|
*/

\define('TB_PHASE18_PLAYER_IDENTIFIER_KEYS', [
    'player_id',
    'identity_id',
]);

/*
|--------------------------------------------------------------------------
| Cross-Region API Shape Contract
|--------------------------------------------------------------------------
|
| Required keys:
| - region_id (string)
| - tournament_id (string)
| - payload (array)
| - meta (array)
|
*/

\define('TB_PHASE18_CROSS_REGION_API_KEYS', [
    'region_id',
    'tournament_id',
    'payload',
    'meta',
]);

/*
|--------------------------------------------------------------------------
| Metadata Contract
|--------------------------------------------------------------------------
|
| Required keys:
| - request_id (string)
| - timestamp (int)
| - source (string)
|
*/

\define('TB_PHASE18_API_META_KEYS', [
    'request_id',
    'timestamp',
    'source',
]);
<