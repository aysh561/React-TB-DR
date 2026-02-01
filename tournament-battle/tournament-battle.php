<?php
/**
 * Plugin Name: Tournament Battle Core
 * Description: Core CPT, Meta, REST, and UI bootstrap for Tournament Battle System.
 * Version: 1.0.0
 * Text Domain: tournament-battle-core
 */

if (!defined('ABSPATH')) exit;

define('TB_CORE_VERSION', '1.0.0');
define('TB_CORE_PATH', plugin_dir_path(__FILE__));
define('TB_CORE_URL', plugin_dir_url(__FILE__));

/**
 * Core Includes (UNCHANGED LOGIC)
 */
require_once TB_CORE_PATH . 'includes/cpt-register.php';
require_once TB_CORE_PATH . 'includes/meta-register.php';
require_once TB_CORE_PATH . 'includes/rest-register.php';

/**
 * UI (Phase-6 baseline)
 */
require_once TB_CORE_PATH . 'includes/shortcodes/ui-shortcode.php';

/**
 * Phase-5 — Payments (PATH UPDATED ONLY)
 */
require_once TB_CORE_PATH . 'includes/phase-5-payments/payment-db.php';

/**
 * Phase Loaders (STRICT ORDER)
 */
require_once TB_CORE_PATH . 'includes/phase-15-governance/phase-15-loader.php';
require_once TB_CORE_PATH . 'includes/phase-15-realtime/phase-15-realtime-loader.php';
require_once TB_CORE_PATH . 'includes/phase-16-anti-cheat/phase-16-anti-cheat-loader.php';
require_once TB_CORE_PATH . 'includes/phase-17-ai-coaching/index.php';
require_once TB_CORE_PATH . 'includes/phase-18-global-infrastructure/phase-18-loader.php';

/**
 * Activation (DO NOT TOUCH)
 */
function tb_core_activate() {
    tb_register_tournament_cpt();
    flush_rewrite_rules();
    tb_payment_create_table();
}
register_activation_hook(__FILE__, 'tb_core_activate');
