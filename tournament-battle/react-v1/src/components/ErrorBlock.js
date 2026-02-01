import React from "react";
import "../styles/Components.css";

function ErrorBlock({ message }) {
    return (
        <div className="error-block">
            <p className="error-text">{message}</p>
        </div>
    );
}

export default ErrorBlock;
