import { useState, useCallback } from "react";
import { apiClient } from "../api/apiClient";
import { endpoints } from "../api/endpoints";

export function useBracket() {
    const [bracketRounds, setBracketRounds] = useState(null);
    const [bracketNodes, setBracketNodes] = useState(null);
    const [bracketProgression, setBracketProgression] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const getBracketRounds = useCallback(async (tournamentId) => {
        setLoading(true);
        setError(null);

        const res = await apiClient(endpoints.bracket.rounds(tournamentId));

        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }

        setBracketRounds(res.data);
        setLoading(false);
    }, []);

    const getBracketNodes = useCallback(async (tournamentId) => {
        setLoading(true);
        setError(null);

        const res = await apiClient(endpoints.bracket.nodes(tournamentId));

        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }

        setBracketNodes(res.data);
        setLoading(false);
    }, []);

    const getBracketProgression = useCallback(async (tournamentId) => {
        setLoading(true);
        setError(null);

        const res = await apiClient(endpoints.bracket.progression(tournamentId));

        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }

        setBracketProgression(res.data);
        setLoading(false);
    }, []);

    return {
        bracketRounds,
        bracketNodes,
        bracketProgression,
        loading,
        error,
        getBracketRounds,
        getBracketNodes,
        getBracketProgression
    };
}
