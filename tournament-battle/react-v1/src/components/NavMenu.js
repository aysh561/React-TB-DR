import React from "react";
import { NavLink } from "react-router-dom";
import "../styles/Components.css";

function NavMenu() {
    return (
        <nav className="nav-menu">
            <NavLink
                to="/"
                className={({ isActive }) =>
                    isActive ? "nav-item active" : "nav-item"
                }
            >
                Home
            </NavLink>
        </nav>
    );
}

export default NavMenu;
