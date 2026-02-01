<?php
namespace TournamentBattleCore\Bracket;

use TournamentBattleCore\Match\MatchStates;

class Bracket_Engine {

    public function __construct() {
        add_action('tb_match_winner_declared', [$this, 'process_winner'], 10, 1);
    }

    public function process_winner($match) {
        if (!isset($match['match_id'])) {
            return;
        }

        $parsed = Bracket_Helpers::parse_match_id($match['match_id']);
        if ($parsed === null) {
            return;
        }

        $tournament_id = $parsed['tournament_id'];
        $bracket = Bracket_Helpers::get_bracket($tournament_id);
        if (!$bracket) {
            return;
        }

        $updated = $this->propagate_winner($bracket, $match);
        Bracket_Helpers::save_bracket($tournament_id, $updated);

        do_action('tb_bracket_updated', $tournament_id, $updated);
    }

    private function propagate_winner($bracket, $match) {
        $rounds = $bracket['rounds'];

        foreach ($rounds as $round_number => &$round_matches) {
            foreach ($round_matches as $index => &$m) {
                if ($m['match_id'] === $match['match_id']) {
                    $m['winner'] = $match['winner'];

                    $next_round = $round_number + 1;
                    if (!isset($rounds[$next_round])) {
                        continue;
                    }

                    $slot = Bracket_Helpers::resolve_next_slot($index);
                    if (!isset($rounds[$next_round][$slot])) {
                        continue;
                    }

                    $next_match = &$rounds[$next_round][$slot];

                    if ($next_match['player1'] === null) {
                        $next_match['player1'] = $match['winner'];
                    } else {
                        $next_match['player2'] = $match['winner'];
                    }

                    $p1 = $next_match['player1'];
                    $p2 = $next_match['player2'];

                    if ($p1 !== null && $p2 !== null) {
                        $next_match['state'] = MatchStates::READY;
                    } else {
                        $next_match['state'] = MatchStates::PENDING;
                    }
                }
            }
        }

        $bracket['rounds'] = $rounds;
        return $bracket;
    }
}
