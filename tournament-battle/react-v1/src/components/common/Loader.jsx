import React from "react";

function Loader({ size = "md", className = "" }) {
    const sizeClass =
        size === "sm" ? "loader-sm" :
        size === "lg" ? "loader-lg" :
        "loader-md";

    return (
        <div
            className={`loader ${sizeClass} ${className}`.trim()}
        />
    );
}

export default Loader;
