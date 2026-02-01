import React from "react";
import BracketNode from "./BracketNode";

function BracketRound({ round }) {
    if (!round || !round.nodes) return null;

    return (
        <div className="bracket-round">
            <h3 className="bracket-round-title">{round.title}</h3>

            <div className="bracket-nodes">
                {round.nodes.map((node) => (
                    <BracketNode key={node.id} node={node} />
                ))}
            </div>
        </div>
    );
}

export default BracketRound;
