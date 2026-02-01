import React from "react";

function EmptyState({ text, className = "" }) {
    if (!text) return null;

    return (
        <div className={`empty-state ${className}`.trim()}>
            <p className="empty-text">{text}</p>
        </div>
    );
}

export default EmptyState;
