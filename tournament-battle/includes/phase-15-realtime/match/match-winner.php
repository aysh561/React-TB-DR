<?php
namespace TournamentBattleCore\Match;

class MatchWinner {
    public static function resolve($match, $packet1, $packet2) {
        if ($match['state'] !== MatchStates::WAITING_VERIFICATION) {
            return $match;
        }

        $p1 = !empty($packet1['score']);
        $p2 = !empty($packet2['score']);

        if ($p1 && $p2) {
            if ($packet1['score'] > $packet2['score']) {
                $winner = $match['player1'];
            } elseif ($packet2['score'] > $packet1['score']) {
                $winner = $match['player2'];
            } else {
                $match['winner'] = null;
                $match['state'] = MatchStates::DISPUTED;
                do_action('tb_match_winner_declared', $match['match_id'], null);
                return $match;
            }

            $match['winner'] = $winner;
            $match['state'] = MatchStates::COMPLETED;
            do_action('tb_match_winner_declared', $match['match_id'], $winner);
            do_action('tb_next_round_trigger', $match);
            return $match;
        }

        if ($p1 && !$p2) {
            $winner = $match['player1'];
        } elseif ($p2 && !$p1) {
            $winner = $match['player2'];
        } else {
            $match['winner'] = null;
            $match['state'] = MatchStates::DISPUTED;
            do_action('tb_match_winner_declared', $match['match_id'], null);
            return $match;
        }

        $match['winner'] = $winner;
        $match['state'] = MatchStates::COMPLETED;
        do_action('tb_match_winner_declared', $match['match_id'], $winner);
        do_action('tb_next_round_trigger', $match);
        return $match;
    }
}
