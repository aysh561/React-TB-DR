export function parseBracket(rounds) {
    if (!rounds || typeof rounds !== "object") return [];

    const ordered = Object.keys(rounds)
        .map((n) => parseInt(n, 10))
        .sort((a, b) => a - b);

    return ordered.map((roundNumber) => {
        const matchList = Array.isArray(rounds[roundNumber])
            ? rounds[roundNumber]
            : [];

        const matches = matchList.map((m) => ({
            match_id: m.match_id,
            player1: m.player1,
            player2: m.player2,
            state: m.state,
            winner: m.winner
        }));

        return {
            label: `Round ${roundNumber}`,
            matches
        };
    });
}
