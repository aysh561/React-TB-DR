export function formatPlayer(player) {
    if (!player) return "Unknown";
    if (player.name) return player.name;
    if (player.id) return `Player ${player.id}`;
    return "Unknown";
}
