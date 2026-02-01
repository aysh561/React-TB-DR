import React from "react";

function Button({ label, onClick, type = "button", disabled = false, className = "" }) {
    const finalClass = `btn ${disabled ? "btn-disabled" : ""} ${className}`.trim();

    const handleClick = (e) => {
        if (disabled) return;
        if (onClick) onClick(e);
    };

    return (
        <button
            type={type}
            className={finalClass}
            disabled={disabled}
            aria-disabled={disabled}
            onClick={handleClick}
        >
            {label}
        </button>
    );
}

export default Button;
