import React, { useEffect } from "react";
import { useParams } from "react-router-dom";
import Loader from "../components/Loader";
import ErrorBlock from "../components/ErrorBlock";
import EmptyState from "../components/EmptyState";
import BracketNode from "../components/BracketNode";
import { useFetch } from "../hooks/useFetch";
import { parseBracket } from "../utils/parseBracket";
import "../styles/Bracket.css";

function BracketScreen() {
    const { id } = useParams();
    const { data, loading, error, execute } = useFetch();

    useEffect(() => {
        execute(`/api/tournament/${id}/bracket`);
    }, [id, execute]);

    if (loading) return <Loader />;
    if (error) return <ErrorBlock message="Bracket load nahi ho saka." />;
    if (!data || !data.rounds || Object.keys(data.rounds).length === 0) {
        return <EmptyState text="Bracket available nahi." />;
    }

    const rounds = parseBracket(data.rounds);
    if (!rounds || rounds.length === 0) {
        return <EmptyState text="Bracket structure incomplete hai." />;
    }

    return (
        <div className="page-container">
            <h1 className="page-title">Bracket</h1>

            <div className="bracket-container">
                {rounds.map((round, index) => (
                    <div key={index} className="bracket-round">
                        <h2 className="round-title">{round.label}</h2>
                        {(round.matches || []).map((match) => (
                            <BracketNode key={match.match_id} match={match} />
                        ))}
                    </div>
                ))}
            </div>
        </div>
    );
}

export default BracketScreen;
