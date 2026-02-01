import React, { useEffect } from "react";
import { useParams, Link } from "react-router-dom";
import Loader from "../components/Loader";
import ErrorBlock from "../components/ErrorBlock";
import EmptyState from "../components/EmptyState";
import { formatPlayer } from "../utils/formatPlayer";
import { formatTime } from "../utils/formatTime";
import { useFetch } from "../hooks/useFetch";

function MatchDetailScreen() {
    const { mid } = useParams();
    const { data, loading, error, execute } = useFetch();

    useEffect(() => {
        execute(`/api/match/${mid}`);
    }, [mid, execute]);

    if (loading) return <Loader />;
    if (error) return <ErrorBlock message="Match details load nahi ho sakay." />;
    if (!data) return <EmptyState text="Match details nahi milay." />;

    const p1 = typeof data.player1 === "object" ? data.player1 : { id: data.player1 };
    const p2 = typeof data.player2 === "object" ? data.player2 : { id: data.player2 };
    const winner = data.winner
        ? typeof data.winner === "object"
            ? data.winner
            : { id: data.winner }
        : null;

    return (
        <div className="page-container">
            <h1 className="page-title">Match Detail</h1>

            <div className="match-detail-box">
                <div className="detail-row">
                    <span className="detail-label">Player 1:</span>
                    <Link to={`/p/${p1.id}`}>{formatPlayer(p1)}</Link>
                </div>

                <div className="detail-row">
                    <span className="detail-label">Player 2:</span>
                    <Link to={`/p/${p2.id}`}>{formatPlayer(p2)}</Link>
                </div>

                <div className="detail-row">
                    <span className="detail-label">Start Time:</span>
                    <span>{formatTime(data.startTime)}</span>
                </div>

                <div className="detail-row">
                    <span className="detail-label">Status:</span>
                    <span>{data.status}</span>
                </div>

                {winner && (
                    <div className="detail-row winner-row">
                        <span className="detail-label">Winner:</span>
                        <span className="winner-name">{formatPlayer(winner)}</span>
                    </div>
                )}
            </div>
        </div>
    );
}

export default MatchDetailScreen;
