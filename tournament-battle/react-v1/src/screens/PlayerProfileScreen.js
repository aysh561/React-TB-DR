import React, { useEffect } from "react";
import { useParams } from "react-router-dom";
import Loader from "../components/Loader";
import ErrorBlock from "../components/ErrorBlock";
import EmptyState from "../components/EmptyState";
import { useFetch } from "../hooks/useFetch";

function PlayerProfileScreen() {
    const { pid } = useParams();
    const { data, loading, error, execute } = useFetch();

    useEffect(() => {
        execute(`/api/player/${pid}`);
    }, [pid, execute]);

    if (loading) return <Loader />;
    if (error) return <ErrorBlock message="Player profile load nahi ho saka." />;
    if (!data) return <EmptyState text="Player profile available nahi." />;

    const name = data.name || "Unknown";
    const country = data.country || "Unknown";
    const rank = data.rank || "Unknown";
    const matchesPlayed = data.matchesPlayed || 0;
    const wins = data.wins || 0;
    const losses = data.losses || 0;

    return (
        <div className="page-container">
            <h1 className="page-title">Player Profile</h1>

            <div className="player-profile-box">
                <div className="profile-row">
                    <span className="profile-label">Name:</span>
                    <span>{name}</span>
                </div>

                <div className="profile-row">
                    <span className="profile-label">Country:</span>
                    <span>{country}</span>
                </div>

                <div className="profile-row">
                    <span className="profile-label">Rank:</span>
                    <span>{rank}</span>
                </div>

                <div className="profile-row">
                    <span className="profile-label">Matches Played:</span>
                    <span>{matchesPlayed}</span>
                </div>

                <div className="profile-row">
                    <span className="profile-label">Wins:</span>
                    <span>{wins}</span>
                </div>

                <div className="profile-row">
                    <span className="profile-label">Losses:</span>
                    <span>{losses}</span>
                </div>
            </div>
        </div>
    );
}

export default PlayerProfileScreen;
