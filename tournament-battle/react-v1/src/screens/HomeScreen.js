import React, { useEffect } from "react";
import { Link } from "react-router-dom";
import TournamentCard from "../components/TournamentCard";
import Loader from "../components/Loader";
import ErrorBlock from "../components/ErrorBlock";
import EmptyState from "../components/EmptyState";
import { useFetch } from "../hooks/useFetch";

function HomeScreen() {
    const { data, loading, error, execute } = useFetch();

    useEffect(() => {
        execute("/api/tournaments");
    }, [execute]);

    if (loading) return <Loader />;
    if (error) return <ErrorBlock message="Tournament list load nahi ho saki." />;
    if (!data || data.length === 0) return <EmptyState text="Koi tournament nahi mila." />;

    return (
        <div className="page-container">
            <h1 className="page-title">Tournaments</h1>

            <div className="card-grid">
                {data.map((item) => (
                    <Link key={item.id} to={`/t/${item.id}`}>
                        <TournamentCard tournament={item} />
                    </Link>
                ))}
            </div>
        </div>
    );
}

export default HomeScreen;
