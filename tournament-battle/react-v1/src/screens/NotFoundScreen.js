import React from "react";
import { Link } from "react-router-dom";

function NotFoundScreen() {
    return (
        <div className="page-container">
            <h1 className="page-title">404 — Page Not Found</h1>
            <div className="notfound-box">
                <p>Requested page exist nahi karti.</p>
                <Link className="btn-primary" to="/">Home Page par jao</Link>
            </div>
        </div>
    );
}

export default NotFoundScreen;
