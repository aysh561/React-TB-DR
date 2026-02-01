<?php
namespace TournamentBattleCore\Bracket;

class Bracket_Feed {

    public static function get_bracket_feed($tournament_id) {
        $bracket = Bracket_Helpers::get_bracket($tournament_id);

        if (!$bracket) {
            return [
                'tournament_id' => $tournament_id,
                'rounds' => []
            ];
        }

        ksort($bracket['rounds']);

        $feed = [
            'tournament_id' => $tournament_id,
            'rounds' => []
        ];

        foreach ($bracket['rounds'] as $round => $matches) {
            ksort($matches);

            $feed['rounds'][$round] = [];
            foreach ($matches as $m) {
                $feed['rounds'][$round][] = [
                    'match_id' => $m['match_id'],
                    'player1'  => $m['player1'],
                    'player2'  => $m['player2'],
                    'state'    => $m['state'],
                    'winner'   => $m['winner']
                ];
            }
        }

        return $feed;
    }
}
