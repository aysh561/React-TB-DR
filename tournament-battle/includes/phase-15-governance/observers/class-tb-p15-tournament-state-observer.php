<?php
/**
 * Phase 15 — Tournament State Observer
 * File: /includes/phase-15-governance/observers/class-tb-p15-tournament-state-observer.php
 */

namespace TB\Phase15\Governance\Observers;

use TB\Phase15\Governance\Helpers\TB_P15_Context;
use TB\Phase15\Governance\Helpers\TB_P15_Idempotency;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_Tournament_State_Observer
{
    /**
     * Register observer hooks
     *
     * @return void
     */
    public static function register(): void
    {
        add_action('tb_tournament_created',        [__CLASS__, 'on_created'],        10, 1);
        add_action('tb_tournament_join_closed',    [__CLASS__, 'on_join_closed'],    10, 1);
        add_action('tb_tournament_started',        [__CLASS__, 'on_started'],        10, 1);
        add_action('tb_tournament_completed',      [__CLASS__, 'on_completed'],      10, 1);
        add_action('tb_tournament_cancelled',      [__CLASS__, 'on_cancelled'],      10, 1);
    }

    public static function on_created($tournament_id): void
    {
        self::dispatch('tournament_created', $tournament_id);
    }

    public static function on_join_closed($tournament_id): void
    {
        self::dispatch('tournament_join_closed', $tournament_id);
    }

    public static function on_started($tournament_id): void
    {
        self::dispatch('tournament_started', $tournament_id);
    }

    public static function on_completed($tournament_id): void
    {
        self::dispatch('tournament_completed', $tournament_id);
    }

    public static function on_cancelled($tournament_id): void
    {
        self::dispatch('tournament_cancelled', $tournament_id);
    }

    /**
     * Build context, check idempotency, and emit deterministic signal
     *
     * @param string $event
     * @param mixed  $tournament_id
     * @return void
     */
    private static function dispatch(string $event, $tournament_id): void
    {
        if (empty($tournament_id)) {
            return;
        }

        $context = TB_P15_Context::build([
            'event'     => $event,
            'entity_id' => $tournament_id,
            'source'    => 'observer:tournament_state',
        ]);

        if (empty($context['event']) || empty($context['entity_id'])) {
            return;
        }

        $idem = TB_P15_Idempotency::acquire(
            $context['event'],
            $context['entity_id'],
            $context
        );

        if ($idem['allowed'] !== true) {
            return;
        }

        do_action('tb_p15_' . $event, $context);
    }
}
