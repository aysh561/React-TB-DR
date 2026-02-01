<?php
namespace TournamentBattleCore\Match;

class MatchEngine {
    public static function create_match($data) {
        $data['state'] = MatchStates::PENDING;
        $data['winner'] = null;
        do_action('tb_match_created', $data);
        return $data;
    }

    public static function update_state($match, $new_state, $packet1 = [], $packet2 = []) {
        if (!MatchStates::can_transition($match['state'], $new_state)) {
            return $match;
        }

        $old_state = $match['state'];
        $match['state'] = $new_state;

        if (
            $old_state === MatchStates::WAITING_VERIFICATION &&
            ($new_state === MatchStates::COMPLETED || $new_state === MatchStates::DISPUTED)
        ) {
            $match = MatchWinner::resolve($match, $packet1, $packet2);
        }

        do_action('tb_match_state_changed', $match['match_id'], $old_state, $new_state);
        return $match;
    }
}
