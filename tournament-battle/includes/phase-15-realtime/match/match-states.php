<?php
namespace TournamentBattleCore\Match;

class MatchStates {
    const PENDING = 'pending';
    const READY = 'ready';
    const IN_PROGRESS = 'in_progress';
    const WAITING_VERIFICATION = 'waiting_verification';
    const COMPLETED = 'completed';
    const DISPUTED = 'disputed';
    const CANCELLED = 'cancelled';

    public static function can_transition($from, $to) {
        $map = [
            self::PENDING => [self::READY, self::CANCELLED],
            self::READY => [self::IN_PROGRESS, self::CANCELLED],
            self::IN_PROGRESS => [self::WAITING_VERIFICATION, self::CANCELLED],
            self::WAITING_VERIFICATION => [self::COMPLETED, self::DISPUTED, self::CANCELLED],
            self::COMPLETED => [],
            self::DISPUTED => [],
            self::CANCELLED => []
        ];
        return in_array($to, $map[$from], true);
    }
}
