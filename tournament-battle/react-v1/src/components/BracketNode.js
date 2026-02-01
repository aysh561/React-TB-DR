import React from "react";
import "../styles/Bracket.css";
import { formatPlayer } from "../utils/formatPlayer";

function BracketNode({ match }) {
    const p1 = typeof match.player1 === "object" ? match.player1 : { id: match.player1 };
    const p2 = typeof match.player2 === "object" ? match.player2 : { id: match.player2 };
    const winner = match.winner
        ? typeof match.winner === "object"
            ? match.winner
            : { id: match.winner }
        : null;

    return (
        <div className="bracket-node">
            <div className="bracket-player">
                <span className={winner && String(winner.id) === String(p1.id) ? "winner" : ""}>
                    {formatPlayer(p1)}
                </span>
            </div>

            <div className="bracket-player">
                <span className={winner && String(winner.id) === String(p2.id) ? "winner" : ""}>
                    {formatPlayer(p2)}
                </span>
            </div>

            <div className={`bracket-state state-${match.state}`}>
                <span>{match.state}</span>
            </div>
        </div>
    );
}

export default BracketNode;
