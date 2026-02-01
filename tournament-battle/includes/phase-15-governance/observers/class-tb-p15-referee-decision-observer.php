<?php
/**
 * Phase 15 — Referee Decision Observer
 * File: /includes/phase-15-governance/observers/class-tb-p15-referee-decision-observer.php
 */

namespace TB\Phase15\Governance\Observers;

use TB\Phase15\Governance\Helpers\TB_P15_Context;
use TB\Phase15\Governance\Helpers\TB_P15_Idempotency;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_Referee_Decision_Observer
{
    /**
     * Register referee decision hooks
     *
     * @return void
     */
    public static function register(): void
    {
        add_action('tb_referee_decision_recorded',   [__CLASS__, 'on_recorded'],   10, 1);
        add_action('tb_referee_decision_finalized',  [__CLASS__, 'on_finalized'],  10, 1);
        add_action('tb_referee_decision_overturned', [__CLASS__, 'on_overturned'], 10, 1);
    }

    public static function on_recorded($decision_ref): void
    {
        self::dispatch('referee_decision_recorded', $decision_ref);
    }

    public static function on_finalized($decision_ref): void
    {
        self::dispatch('referee_decision_finalized', $decision_ref);
    }

    public static function on_overturned($decision_ref): void
    {
        self::dispatch('referee_decision_overturned', $decision_ref);
    }

    /**
     * Build context, check idempotency, and emit referee signal
     *
     * @param string $event
     * @param mixed  $decision_ref
     * @return void
     */
    private static function dispatch(string $event, $decision_ref): void
    {
        if (empty($decision_ref)) {
            return;
        }

        $context = TB_P15_Context::build([
            'event'     => $event,
            'entity_id' => $decision_ref,
            'source'    => 'observer:referee_decision',
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

        do_action('tb_p15_referee_' . $event, $context);
    }
}
