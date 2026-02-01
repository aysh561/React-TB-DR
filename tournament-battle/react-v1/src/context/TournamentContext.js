import React, { createContext, useState, useCallback } from "react";

export const TournamentContext = createContext();

export function TournamentProvider({ children }) {
    const [selectedTournament, setSelectedTournament] = useState(null);

    const selectTournament = useCallback((t) => {
        setSelectedTournament(t);
    }, []);

    const clearTournament = useCallback(() => {
        setSelectedTournament(null);
    }, []);

    return (
        <TournamentContext.Provider
            value={{
                selectedTournament,
                selectTournament,
                clearTournament
            }}
        >
            {children}
        </TournamentContext.Provider>
    );
}
