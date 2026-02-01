import React from "react";
import { Link } from "react-router-dom";
import NavMenu from "./NavMenu";
import "../styles/Components.css";

function Header() {
    return (
        <header className="header">
            <div className="header-inner">
                <Link to="/" className="header-logo">Tournament Battle</Link>
                <NavMenu />
            </div>
        </header>
    );
}

export default Header;
