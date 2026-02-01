import React, { useEffect } from "react";
import { useParams, Link } from "react-router-dom";
import Loader from "../components/Loader";
import ErrorBlock from "../components/ErrorBlock";
import EmptyState from "../components/EmptyState";
import { useFetch } from "../hooks/useFetch";

function TournamentScreen() {
    const { id } = useParams();
    const { data, loading, error, execute } = useFetch();

    useEffect(() => {
        execute(`/api/tournament/${id}`);
    }, [id, execute]);

    if (loading) return <Loader />;
    if (error) return <ErrorBlock message="Tournament detail load nahi ho saki." />;
    if (!data) return <EmptyState text="Tournament detail available nahi." />;

    return (
        <div className="page-container">
            <h1 className="page-title">{data.title}</h1>

            <div className="tournament-info">
                <div className="info-row">
                    <span className="info-label">Start Time:</span>
                    <span>{data.startTime}</span>
                </div>
                <div className="info-row">
                    <span className="info-label">Format:</span>
                    <span>{data.format}</span>
                </div>
                <div className="info-row">
                    <span className="info-label">Status:</span>
                    <span>{data.status}</span>
                </div>
            </div>

            <div className="tournament-nav">
                <Link className="btn-primary" to={`/t/${id}/m`}>Matches</Link>
                <Link className="btn-primary" to={`/t/${id}/b`}>Bracket</Link>
            </div>
        </div>
    );
}

export default TournamentScreen;
