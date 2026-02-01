import React, { useEffect } from "react";
import { useParams, Link } from "react-router-dom";
import Loader from "../components/Loader";
import ErrorBlock from "../components/ErrorBlock";
import EmptyState from "../components/EmptyState";
import MatchCard from "../components/MatchCard";
import { useFetch } from "../hooks/useFetch";

function MatchesScreen() {
    const { id } = useParams();
    const { data, loading, error, execute } = useFetch();

    useEffect(() => {
        execute(`/api/tournament/${id}/matches`);
    }, [id, execute]);

    if (loading) return <Loader />;
    if (error) return <ErrorBlock message="Matches list load nahi ho saki." />;
    if (!data || data.length === 0) return <EmptyState text="Is tournament me koi match nahi hai." />;

    return (
        <div className="page-container">
            <h1 className="page-title">Matches</h1>

            <div className="card-grid">
                {data.map((match) => (
                    <Link key={match.id} to={`/m/${match.id}`}>
                        <MatchCard match={match} />
                    </Link>
                ))}
            </div>
        </div>
    );
}

export default MatchesScreen;
