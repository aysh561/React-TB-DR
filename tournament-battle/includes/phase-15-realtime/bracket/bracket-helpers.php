<?php
namespace TournamentBattleCore\Bracket;

class Bracket_Helpers {

    public static function get_bracket($tournament_id) {
        return get_option('tb_bracket_' . $tournament_id);
    }

    public static function save_bracket($tournament_id, $bracket) {
        update_option('tb_bracket_' . $tournament_id, $bracket);
    }

    public static function parse_match_id($match_id) {
        if (!preg_match('/^t_(\d+)_r_(\d+)_m_(\d+)$/', $match_id, $m)) {
            return null;
        }

        return [
            'tournament_id' => (int) $m[1],
            'round'         => (int) $m[2],
            'slot'          => (int) $m[3]
        ];
    }

    public static function build_match_id($tournament_id, $round, $slot) {
        return 't_' . $tournament_id . '_r_' . $round . '_m_' . $slot;
    }

    public static function resolve_next_slot($index) {
        return (int) floor($index / 2);
    }
}
