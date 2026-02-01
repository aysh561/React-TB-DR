import React from "react";
import BracketRound from "./BracketRound";
import Loader from "../common/Loader";
import ErrorBlock from "../common/ErrorBlock";
import EmptyState from "../common/EmptyState";

function BracketView({ rounds, loading, error }) {
    if (loading) return <Loader />;
    if (error) return <ErrorBlock message={error} />;
    if (!rounds || rounds.length === 0) {
        return <EmptyState text="No bracket data found" />;
    }

    return (
        <div className="bracket-view">
            {rounds.map((round) => (
                <BracketRound key={round.id} round={round} />
            ))}
        </div>
    );
}

export default BracketView;
