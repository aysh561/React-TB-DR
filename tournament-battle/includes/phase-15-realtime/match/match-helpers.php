<?php
namespace TournamentBattleCore\Match;

class MatchHelpers {
    public static function get_match_by_id($matches, $id) {
        foreach ($matches as $m) {
            if ($m['match_id'] === $id) return $m;
        }
        return null;
    }

    public static function get_round_matches($matches, $round) {
        $list = [];
        foreach ($matches as $m) {
            if (strpos($m['match_id'], "_r_{$round}_") !== false) {
                $list[] = $m;
            }
        }
        return $list;
    }

    public static function get_next_round_match_slot($tournament_id, $round, $slot) {
        return MatchGenerator::build_match_id($tournament_id, $round + 1, $slot);
    }

    public static function assign_winner_to_next_match(&$next_matches, $next_id, $winner) {
        foreach ($next_matches as &$m) {
            if ($m['match_id'] === $next_id) {
                if (empty($m['player1'])) $m['player1'] = $winner;
                else $m['player2'] = $winner;
            }
        }
        return $next_matches;
    }
}
