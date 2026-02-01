import React from "react";
import { Routes, Route } from "react-router-dom";

import HomeScreen from "../screens/HomeScreen";
import TournamentScreen from "../screens/TournamentScreen";
import MatchesScreen from "../screens/MatchesScreen";
import BracketScreen from "../screens/BracketScreen";
import MatchDetailScreen from "../screens/MatchDetailScreen";
import PlayerProfileScreen from "../screens/PlayerProfileScreen";
import NotFoundScreen from "../screens/NotFoundScreen";

function AppRoutes() {
    return (
        <Routes>
            <Route path="/" element={<HomeScreen />} />
            <Route path="/t/:id" element={<TournamentScreen />} />
            <Route path="/t/:id/m" element={<MatchesScreen />} />
            <Route path="/t/:id/b" element={<BracketScreen />} />
            <Route path="/m/:mid" element={<MatchDetailScreen />} />
            <Route path="/p/:pid" element={<PlayerProfileScreen />} />
            <Route path="*" element={<NotFoundScreen />} />
        </Routes>
    );
}

export default AppRoutes;
