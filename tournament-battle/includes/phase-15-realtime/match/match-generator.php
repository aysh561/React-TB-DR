<?php
namespace TournamentBattleCore\Match;

class MatchGenerator {
    public static function generate_round_one($tournament_id, $players) {
        $matches = [];
        $count = count($players);
        $i = 0;
        $round = 1;
        $match_number = 1;

        while ($i < $count) {
            if ($i + 1 < $count) {
                $matches[] = [
                    'match_id' => self::build_match_id($tournament_id, $round, $match_number),
                    'player1' => $players[$i],
                    'player2' => $players[$i + 1],
                    'state' => MatchStates::PENDING,
                    'winner' => null
                ];
                $match_number++;
                $i += 2;
            } else {
                $matches[] = [
                    'match_id' => self::build_match_id($tournament_id, $round, $match_number),
                    'player1' => $players[$i],
                    'player2' => null,
                    'state' => MatchStates::COMPLETED,
                    'winner' => $players[$i]
                ];
                $match_number++;
                $i++;
            }
        }

        do_action('tb_matches_generated', $tournament_id, $matches);
        return $matches;
    }

    public static function build_match_id($tournament_id, $round, $num) {
        return "t_{$tournament_id}_r_{$round}_m_{$num}";
    }
}
