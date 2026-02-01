import React from "react";
import "../styles/Components.css";

function EmptyState({ text }) {
    return (
        <div className="empty-state">
            <p className="empty-text">{text}</p>
        </div>
    );
}

export default EmptyState;
