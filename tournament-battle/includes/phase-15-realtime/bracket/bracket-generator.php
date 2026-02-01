<?php
namespace TournamentBattleCore\Bracket;

class Bracket_Generator {

    public static function generate_bracket($tournament_id, $matches) {
        $rounds = [];
        $current_round = 1;

        $current_matches = [];
        foreach ($matches as $m) {
            $current_matches[] = [
                'match_id' => $m['match_id'],
                'player1'  => $m['player1'],
                'player2'  => $m['player2'],
                'state'    => $m['state'],
                'winner'   => $m['winner']
            ];
        }

        while (count($current_matches) > 0) {
            $rounds[$current_round] = $current_matches;

            $next_round_count = (int) ceil(count($current_matches) / 2);
            $next_matches = [];

            for ($i = 0; $i < $next_round_count; $i++) {
                $next_matches[] = [
                    'match_id' => self::build_match_id($tournament_id, $current_round + 1, $i),
                    'player1'  => null,
                    'player2'  => null,
                    'state'    => 'pending',
                    'winner'   => null
                ];
            }

            $current_matches = $next_matches;
            $current_round++;
        }

        return [
            'tournament_id' => $tournament_id,
            'rounds' => $rounds
        ];
    }

    private static function build_match_id($tournament_id, $round, $slot) {
        return 't_' . $tournament_id . '_r_' . $round . '_m_' . $slot;
    }
}
