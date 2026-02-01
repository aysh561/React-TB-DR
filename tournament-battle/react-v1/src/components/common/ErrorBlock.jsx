import React from "react";

function ErrorBlock({ message, className = "" }) {
    if (!message) return null;

    return (
        <div className={`error-block ${className}`.trim()}>
            <p className="error-text">{message}</p>
        </div>
    );
}

export default ErrorBlock;
