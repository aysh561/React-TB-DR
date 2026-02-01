import React from "react";
import "../styles/Cards.css";
import { formatTime } from "../utils/formatTime";

function TournamentCard({ tournament }) {
    return (
        <div className="card tournament-card">
            <h3 className="card-title">{tournament.title}</h3>

            <div className="card-row">
                <span className="card-label">Start:</span>
                <span>{formatTime(tournament.startTime)}</span>
            </div>

            <div className="card-row">
                <span className="card-label">Format:</span>
                <span>{tournament.format}</span>
            </div>

            <div className="card-row">
                <span className="card-label">Status:</span>
                <span>{tournament.status}</span>
            </div>
        </div>
    );
}

export default TournamentCard;
