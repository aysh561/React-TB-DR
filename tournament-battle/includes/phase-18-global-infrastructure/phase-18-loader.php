<?php
declare(strict_types=1);

namespace TB\Phase18\Loader;

/**
 * Phase 18 — Global Tournament Infrastructure
 * FILE 1 / N — Master Loader (STRICT)
 */
final class Phase18Loader
{
    /** @var array<string,bool> */
    private array $environmentChecks = [];

    public function __construct()
    {
        $this->verifyEnvironment();
        $this->bootstrap();
        $this->wireDependencies();
        $this->registerLifecycleHooks();
    }

    /**
     * Phase 18 environment verify
     * yahan sirf hard checks hon ge, koi fallback nahi
     */
    private function verifyEnvironment(): void
    {
        $required = [
            'TB_PHASE_15_READY',
            'TB_PHASE_16_READY',
            'TB_PHASE_17_READY',
        ];

        foreach ($required as $constant) {
            if (!\defined($constant) || \constant($constant) !== true) {
                throw new \RuntimeException(
                    "Phase 18 hard fail: required constant missing or false: {$constant}"
                );
            }
            $this->environmentChecks[$constant] = true;
        }
    }

    /**
     * Phase 18 bootstrap
     * sirf flags / boundaries establish karni hain
     */
    private function bootstrap(): void
    {
        if (\defined('TB_PHASE_18_BOOTSTRAPPED')) {
            throw new \RuntimeException(
                'Phase 18 hard fail: duplicate bootstrap detected'
            );
        }

        \define('TB_PHASE_18_BOOTSTRAPPED', true);
    }

    /**
     * Dependency wiring (ORDER STRICTLY LOCKED)
     */
    private function wireDependencies(): void
    {
        $this->loadContracts();
        $this->loadRegionRegistry();
        $this->bindRealtimeMesh();
        $this->loadTournamentGlobalEngine();
        $this->loadPaymentsGlobalRouter();
        $this->loadIdentityAndReputation();
        $this->attachAntiCheatCluster();
        $this->loadSpectatorNetwork();
        $this->loadUIGlobalModels();
        $this->loadObservabilityAndGovernance();
    }

    private function loadContracts(): void
    {
        $this->requireFile('contracts/contracts-loader.php');
    }

    private function loadRegionRegistry(): void
    {
        $this->requireFile('regions/region-registry.php');
    }

    private function bindRealtimeMesh(): void
    {
        $this->requireFile('bridges/realtime-mesh-readonly.php');
    }

    private function loadTournamentGlobalEngine(): void
    {
        $this->requireFile('tournament/global-engine.php');
    }

    private function loadPaymentsGlobalRouter(): void
    {
        $this->requireFile('payments/global-router.php');
    }

    private function loadIdentityAndReputation(): void
    {
        $this->requireFile('identity/identity-reputation.php');
    }

    private function attachAntiCheatCluster(): void
    {
        $this->requireFile('bridges/anti-cheat-readonly.php');
    }

    private function loadSpectatorNetwork(): void
    {
        $this->requireFile('spectator/spectator-network.php');
    }

    private function loadUIGlobalModels(): void
    {
        $this->requireFile('ui/global-models.php');
    }

    private function loadObservabilityAndGovernance(): void
    {
        $this->requireFile('governance/observability-governance.php');
    }

    /**
     * Hard include helper
     */
    private function requireFile(string $relativePath): void
    {
        $phase18Root = \dirname(__FILE__);
        $fullPath = $phase18Root . '/' . ltrim($relativePath, '/');

        if (!\is_file($fullPath)) {
            throw new \RuntimeException(
                "Phase 18 hard fail: dependency file missing: {$relativePath}"
            );
        }

        require_once $fullPath;
    }

    /**
     * Global lifecycle hooks
     */
    private function registerLifecycleHooks(): void
    {
        $this->registerInitHook();
        $this->registerShutdownHook();
        $this->registerHealthCheck();
    }

    private function registerInitHook(): void
    {
        if (!\function_exists('tb_phase18_init')) {
            \add_action('init', 'tb_phase18_init');
        }
    }

    private function registerShutdownHook(): void
    {
        \register_shutdown_function(function (): void {
            // intentionally empty — no state persistence
        });
    }

    private function registerHealthCheck(): void
    {
        // intentionally empty — global health check declared outside
    }
}

/**
 * Global init hook (EMPTY BY DESIGN)
 */
function tb_phase18_init(): void
{
    // intentionally empty — orchestration only
}

/**
 * Global health check
 */
function tb_phase18_health_check(): array
{
    return [
        'phase' => 18,
        'status' => 'alive',
        'bootstrapped' => \defined('TB_PHASE_18_BOOTSTRAPPED'),
    ];
}