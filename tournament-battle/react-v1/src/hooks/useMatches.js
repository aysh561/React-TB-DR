import { useState, useCallback } from "react";
import { apiClient } from "../api/apiClient";
import { endpoints } from "../api/endpoints";

export function useMatches() {
    const [matches, setMatches] = useState(null);
    const [singleMatch, setSingleMatch] = useState(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    const getMatches = useCallback(async (tournamentId) => {
        setLoading(true);
        setError(null);
        const res = await apiClient(endpoints.matches.list(tournamentId));
        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }
        setMatches(res.data);
        setLoading(false);
    }, []);

    const getMatch = useCallback(async (matchId) => {
        setLoading(true);
        setError(null);
        const res = await apiClient(endpoints.matches.details(matchId));
        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return;
        }
        setSingleMatch(res.data);
        setLoading(false);
    }, []);

    const submitVerification = useCallback(async (matchId, payload) => {
        setLoading(true);
        setError(null);
        const res = await apiClient(endpoints.matches.submitVerification(matchId), {
            method: "POST",
            body: payload
        });
        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return null;
        }
        setLoading(false);
        return res.data;
    }, []);

    const updateMatchState = useCallback(async (matchId, payload) => {
        setLoading(true);
        setError(null);
        const res = await apiClient(endpoints.matches.updateState(matchId), {
            method: "PATCH",
            body: payload
        });
        if (!res.ok) {
            setError(res.message);
            setLoading(false);
            return null;
        }
        setLoading(false);
        return res.data;
    }, []);

    return {
        matches,
        singleMatch,
        loading,
        error,
        getMatches,
        getMatch,
        submitVerification,
        updateMatchState
    };
}
