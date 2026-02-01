<?php
/**
 * Phase 15 — Payment State Observer
 * File: /includes/phase-15-governance/observers/class-tb-p15-payment-state-observer.php
 */

namespace TB\Phase15\Governance\Observers;

use TB\Phase15\Governance\Helpers\TB_P15_Context;
use TB\Phase15\Governance\Helpers\TB_P15_Idempotency;

if (!defined('ABSPATH')) {
    exit;
}

class TB_P15_Payment_State_Observer
{
    /**
     * Register payment lifecycle hooks
     *
     * @return void
     */
    public static function register(): void
    {
        add_action('tb_payment_initiated',     [__CLASS__, 'on_initiated'],     10, 1);
        add_action('tb_payment_submitted',     [__CLASS__, 'on_submitted'],     10, 1);
        add_action('tb_payment_under_review',  [__CLASS__, 'on_under_review'],  10, 1);
        add_action('tb_payment_verified',      [__CLASS__, 'on_verified'],      10, 1);
        add_action('tb_payment_rejected',      [__CLASS__, 'on_rejected'],      10, 1);
        add_action('tb_payment_failed',        [__CLASS__, 'on_failed'],        10, 1);
    }

    public static function on_initiated($payment_id): void
    {
        self::dispatch('payment_initiated', $payment_id);
    }

    public static function on_submitted($payment_id): void
    {
        self::dispatch('payment_submitted', $payment_id);
    }

    public static function on_under_review($payment_id): void
    {
        self::dispatch('payment_under_review', $payment_id);
    }

    public static function on_verified($payment_id): void
    {
        self::dispatch('payment_verified', $payment_id);
    }

    public static function on_rejected($payment_id): void
    {
        self::dispatch('payment_rejected', $payment_id);
    }

    public static function on_failed($payment_id): void
    {
        self::dispatch('payment_failed', $payment_id);
    }

    /**
     * Build context, check idempotency, and emit payment signal
     *
     * @param string $event
     * @param mixed  $payment_id
     * @return void
     */
    private static function dispatch(string $event, $payment_id): void
    {
        if (empty($payment_id)) {
            return;
        }

        $context = TB_P15_Context::build([
            'event'     => $event,
            'entity_id' => $payment_id,
            'source'    => 'observer:payment_state',
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

        do_action('tb_p15_payment_' . str_replace('payment_', '', $event), $context);
    }
}
