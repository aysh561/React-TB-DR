import React from "react";
import { formatPlayer } from "../../utils/formatPlayer";

function BracketNode({ node }) {
    return (
        <div className="bracket-node">
            <div className="bracket-row">
                <span className="bracket-label">P1:</span>
                <span>{formatPlayer(node.player1)}</span>
            </div>

            <div className="bracket-row">
                <span className="bracket-label">P2:</span>
                <span>{formatPlayer(node.player2)}</span>
            </div>

            <div className="bracket-row">
                <span className="bracket-label">Winner:</span>
                <span>{node.winner ? formatPlayer(node.winner) : "-"}</span>
            </div>

            <div className="bracket-row">
                <span className="bracket-label">Round:</span>
                <span>{node.round}</span>
            </div>
        </div>
    );
}

export default BracketNode;
