import React from "react";
import { formatPlayer } from "../../utils/formatPlayer";
import { formatTime } from "../../utils/formatTime";

function MatchCard({ match }) {
    const p1 =
        typeof match.player1 === "object"
            ? match.player1
            : { id: match.player1 };

    const p2 =
        typeof match.player2 === "object"
            ? match.player2
            : { id: match.player2 };

    return (
        <div className="card match-card">
            <h3 className="card-title">Match</h3>

            <div className="card-row">
                <span className="card-label">Player 1:</span>
                <span>{formatPlayer(p1)}</span>
            </div>

            <div className="card-row">
                <span className="card-label">Player 2:</span>
                <span>{formatPlayer(p2)}</span>
            </div>

            <div className="card-row">
                <span className="card-label">Start:</span>
                <span>{formatTime(match.startTime)}</span>
            </div>

            <div className="card-row">
                <span className="card-label">State:</span>
                <span>{match.state}</span>
            </div>
        </div>
    );
}

export default MatchCard;
