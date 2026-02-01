import React from "react";

function PageContainer({ children, title, className = "" }) {
    return (
        <div className={`page-container ${className}`.trim()}>
            {title && <h2 className="page-title">{title}</h2>}
            <div className="page-content">
                {children}
            </div>
        </div>
    );
}

export default PageContainer;
