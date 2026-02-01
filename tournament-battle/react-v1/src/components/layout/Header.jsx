import React from "react";
import { Link } from "react-router-dom";

function Header({ user, onLogout }) {
    return (
        <header className="app-header">
            <div className="header-left">
                <Link to="/" className="header-logo">Tournament Battle</Link>
            </div>

            <nav className="header-nav">
                {user ? (
                    <>
                        <Link to="/tournaments" className="header-link">Tournaments</Link>
                        <Link to="/my-tournaments" className="header-link">My Tournaments</Link>
                        <button className="header-link logout-btn" onClick={onLogout}>
                            Logout
                        </button>
                    </>
                ) : (
                    <>
                        <Link to="/login" className="header-link">Login</Link>
                        <Link to="/register" className="header-link">Register</Link>
                    </>
                )}
            </nav>
        </header>
    );
}

export default Header;
